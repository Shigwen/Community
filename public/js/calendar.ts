{
    const CONTAINER: HTMLElement|null = document.querySelector("calendar-wrapper");
    const RAID_CONTAINER: HTMLElement|null = document.querySelector("raid-wrapper");
    const SELECT_CHARACTER: HTMLSelectElement|null = document.querySelector("#raid_character_userCharacter");
    const SELECT_NUMBER_OF_RESULT_PER_PAGE: HTMLSelectElement|null = document.querySelector("#nbrOfResultPerPage");

    if (CONTAINER === null)
    {
        throw new Error("Missing calendar wrapper");
    }

    const NOW: Date = new Date();
    const STORED_MONTHS: Array<HTMLElement> = Array.from(CONTAINER.querySelectorAll("widget-calendar"));

    if (STORED_MONTHS.length < 1)
    {
        throw new Error("Missing elements");
    }

    let chosen_date: string = "";
    let chosen_character: string = "";
    let chosen_number_of_result_per_page: string = "";
    let last_shown_month: number = STORED_MONTHS.length - 1;
    let abort_handle: AbortController|null = null;

    function clear_process_queue(): void
    {
        if (abort_handle)
        {
            abort_handle.abort();
            abort_handle = null;
        }
    }

    async function select_date(target: HTMLLIElement|null = null): Promise<void>
    {
        try
        {
            clear_process_queue();

            let DATE_IDENTIFIER: string|undefined;
            DATE_IDENTIFIER = target.dataset.date;

            if (!DATE_IDENTIFIER)
            {
                throw new Error("Missing Attribute");
            }

            if (target.matches(".is-notavailable"))
            {
                return;
            }

            const OLD_DATE : HTMLLIElement|null = CONTAINER.querySelector('li#is-selected');
            if (OLD_DATE) {
                OLD_DATE.removeAttribute('id');
            }
            target.id = 'is-selected';

            chosen_date = DATE_IDENTIFIER;
            chosen_character = SELECT_CHARACTER.value;
            chosen_number_of_result_per_page = SELECT_NUMBER_OF_RESULT_PER_PAGE.value;

            const BODY: FormData = new FormData();
            BODY.set("date", chosen_date);
            BODY.set("character", chosen_character)
            BODY.set("numberOfResultPerPage", chosen_number_of_result_per_page);

            send_request(BODY);

        }
        catch (error)
        {
            console.log(error);
        }
    }

    async function change_character(): Promise<void>
    {
        try
        {
            clear_process_queue();

            const OLD_DATE : HTMLLIElement|null = CONTAINER.querySelector('li#is-selected');
            if (!OLD_DATE) {
                return;
            }

            chosen_character = SELECT_CHARACTER.value;

            if (!chosen_character)
            {
                throw new Error("Missing Attribute");
            }

            chosen_number_of_result_per_page = SELECT_NUMBER_OF_RESULT_PER_PAGE.value;

            const BODY: FormData = new FormData();
            BODY.set("character", chosen_character);
            BODY.set("date", chosen_date);
            BODY.set("numberOfResultPerPage", chosen_number_of_result_per_page);

            send_request(BODY);
        }
        catch (error)
        {
            console.log(error);
        }
    }

    async function change_number_of_result_per_page(): Promise<void>
    {
        try
        {
            clear_process_queue();

            const OLD_DATE : HTMLLIElement|null = CONTAINER.querySelector('li#is-selected');
            chosen_character = SELECT_CHARACTER.value;
            chosen_number_of_result_per_page = SELECT_NUMBER_OF_RESULT_PER_PAGE.value;

            const BODY: FormData = new FormData();

            if (OLD_DATE) {
                BODY.set("date", chosen_date);
                BODY.set("character", chosen_character);
            }

            BODY.set("numberOfResultPerPage", chosen_number_of_result_per_page);
            send_request(BODY);
        }
        catch (error)
        {
            console.log(error);
        }
    }

    async function send_request(BODY: FormData)
    {
        try {
            clear_process_queue();

            abort_handle = new AbortController();
            const RESPONSE: Response = await fetch(
                "/ajax/get-all-raid-of-the-day",
                {
                    method: "POST",
                    body: BODY,
                    signal: abort_handle.signal
                }
            );

            if (!RESPONSE.ok)
            {
                throw new Error(RESPONSE.statusText);
            }

            const HTML: string = await RESPONSE.text();
            const DIV: HTMLDivElement = document.createElement("div");
            DIV.innerHTML = HTML;
            const ITEM: HTMLElement|null = DIV.querySelector("raid-list");
            const TITLE: HTMLElement|null = DIV.querySelector("raid-list-title");

            if (!ITEM)
            {
                throw new Error("Invalid response");
            }

            const OLD_RAID_LIST: HTMLElement|null = RAID_CONTAINER.querySelector("raid-list");
            OLD_RAID_LIST.insertAdjacentElement("beforebegin", ITEM);
            OLD_RAID_LIST.remove();

            const OLD_RAID_LIST_TITLE: HTMLElement|null = RAID_CONTAINER.querySelector("raid-list-title");
            OLD_RAID_LIST_TITLE.insertAdjacentElement("beforebegin", TITLE);
            OLD_RAID_LIST_TITLE.remove();
        }
        catch (error)
        {
            console.log(error);
        }
        finally {
            clear_process_queue();
        }
    }

    async function update_calendar(go_forward: boolean): Promise<void>
    {
        try
        {
            clear_process_queue();

            if (go_forward)
            {
                ++last_shown_month;
            }
            else if (last_shown_month >= 1)
            {
                --last_shown_month;
            }
            else
            {
                return;
            }

            if (go_forward)
            {
                if (!STORED_MONTHS[last_shown_month])
                {
                    const MONTH: Date = new Date(NOW.getFullYear(), NOW.getMonth() + last_shown_month, 27, 0, 0, 0, 0);
                    const BODY: FormData = new FormData();
                    BODY.set("date", MONTH.toISOString().substr(0, 10));
                    abort_handle = new AbortController();
                    const RESPONSE: Response = await fetch(
                        "/ajax/get-availability-calendar",
                        {
                            method: "POST",
                            body: BODY,
                            signal: abort_handle.signal
                        }
                    );

                    if (!RESPONSE.ok)
                    {
                        throw new Error(RESPONSE.statusText);
                    }

                    const HTML: string = await RESPONSE.text();
                    const DIV: HTMLDivElement = document.createElement("div");
                    DIV.innerHTML = HTML;
                    const ITEM: HTMLElement|null = DIV.querySelector("widget-calendar");

                    if (!ITEM)
                    {
                        throw new Error("Invalid response");
                    }

                    STORED_MONTHS.push(ITEM);
                }

                STORED_MONTHS[last_shown_month - 1].insertAdjacentElement("afterend", STORED_MONTHS[last_shown_month]);
                STORED_MONTHS[last_shown_month - 1].remove();
            }
            else
            {
                STORED_MONTHS[last_shown_month + 1].insertAdjacentElement("beforebegin", STORED_MONTHS[last_shown_month]);
                STORED_MONTHS[last_shown_month + 1].remove();
            }
        }
        catch (error)
        {
            console.log(error);

            if (go_forward)
            {
                --last_shown_month;
            }
            else
            {
                ++last_shown_month;
            }
        }
        finally
        {
            clear_process_queue();
        }
    }

    CONTAINER.addEventListener(
        "click",
        (event: MouseEvent): void =>
        {
            const TARGET: HTMLElement = event.target as HTMLElement;
            const BUTTON: HTMLButtonElement|null = TARGET.closest("button.next, button.prev");

            if (BUTTON)
            {
                update_calendar(BUTTON.classList.contains("next"));
            }

            const CELL: HTMLLIElement|null = TARGET.closest("li[data-date]:not(.text-secondary):not(#is-selected)") as HTMLLIElement|null;

            if (CELL)
            {
                select_date(CELL);
            }
        }
    );

    SELECT_NUMBER_OF_RESULT_PER_PAGE.addEventListener(
        "change",
        (event: Event): void =>
        {
            change_number_of_result_per_page();
        }
    );

    SELECT_CHARACTER ? SELECT_CHARACTER.addEventListener(
        "change",
        (): void =>
        {
            change_character()
        }
    ) : null;

    // Initialize from storage
    {
        const ITEM: string|null = localStorage.getItem("form-booking");

        if (ITEM)
        {
            const DATES: Array<string> = JSON.parse(ITEM) as Array<string>;

            if (DATES.length === 2 && Date.parse(DATES[0]) > Date.now())
            {
                (async (): Promise<void> =>
                {
                    let i: number = 0;

                    for (let j: number = 0; j < 2; ++j)
                    {
                        const ISO_DATE: string = DATES[j];

                        let done: boolean = false;

                        while (!done)
                        {
                            if (!STORED_MONTHS[i])
                            {
                                // Load new month
                                await update_calendar(true);
                            }

                            const CELL: HTMLLIElement|null = STORED_MONTHS[i].querySelector(`li[data-date="${ISO_DATE}"]`);

                            if (CELL)
                            {
                                // Equivalent to a selecting click
                                await select_date(CELL);
                                done = true;
                            }
                            else
                            {
                                ++i;
                            }
                        }
                    }

                    // Reset if unavailable
                    if (!STORED_MONTHS[i].querySelector("li#is-selected"))
                    {

                        if (i > 0)
                        {
                            const BASE: Element|null = STORED_MONTHS[i].nextElementSibling;

                            STORED_MONTHS[i].remove();
                            STORED_MONTHS[i - 1].remove();

                            if (BASE)
                            {
                                BASE.insertAdjacentElement("beforebegin", STORED_MONTHS[0]);
                                BASE.insertAdjacentElement("beforebegin", STORED_MONTHS[1]);
                            }
                        }
                    }
                }
                )();
            }
            else
            {
                localStorage.removeItem("form-booking");
            }
        }
    }
}
