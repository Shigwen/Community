{
    // Container
    const CONTAINER: HTMLElement|null = document.querySelector("calendar-wrapper");
    const RAID_CONTAINER: HTMLElement|null = document.querySelector("raid-wrapper");
    const BUTTON_PAGE_CONTAINER: HTMLElement|null = document.querySelector("buttons-page");

    // Select list
    const SELECT_CHARACTER: HTMLSelectElement|null = document.querySelector("#raid_character_userCharacter");
    const SELECT_NUMBER_OF_RESULT_PER_PAGE: HTMLSelectElement|null = document.querySelector("#nbrOfResultPerPage");

    // Calendar
    const NOW: Date = new Date();
    const STORED_MONTHS: Array<HTMLElement> = Array.from(CONTAINER.querySelectorAll("widget-calendar"));
    let last_shown_month: number = STORED_MONTHS.length - 1;
    let abort_handle: AbortController|null = null;

    // Raid List
    let stored_raid: Array<HTMLElement> = [];
    let chosen_number_of_result_per_page: string = "";
    let current_page : HTMLButtonElement;

    // Filters for raid list
    let chosen_date: string = "";
    let chosen_character: string = "";
    let chosen_number_of_page: string = "0";

    if (CONTAINER === null)
    {
        throw new Error("Missing calendar wrapper");
    }

    if (STORED_MONTHS.length < 1)
    {
        throw new Error("Missing elements");
    }

    function clear_process_queue(): void
    {
        if (abort_handle)
        {
            abort_handle.abort();
            abort_handle = null;
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

            const RAID_LIST: HTMLElement = await update_raid_list(BODY);
            stored_raid = [];
            stored_raid[0] = RAID_LIST;
        }
        catch (error)
        {
            console.log(error);
        }
        finally
        {
            clear_process_queue();
        }
    }

    async function change_month(go_forward: boolean): Promise<void>
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

                    const ITEM: HTMLElement|null = await update_calendar(BODY);

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
            chosen_number_of_result_per_page = SELECT_NUMBER_OF_RESULT_PER_PAGE.value;

            if (SELECT_CHARACTER) {
                chosen_character = SELECT_CHARACTER.value;
            }

            const BODY: FormData = new FormData();
            BODY.set("date", chosen_date);
            BODY.set("character", chosen_character);
            BODY.set("numberOfResultPerPage", chosen_number_of_result_per_page);

            const RAID_LIST: HTMLElement = await update_raid_list(BODY);
            stored_raid = [];
            stored_raid[0] = RAID_LIST;
        }
        catch (error)
        {
            console.log(error);
        }
        finally
        {
            clear_process_queue();
        }
    }

    async function change_number_of_result_per_page(): Promise<void>
    {
        try
        {
            clear_process_queue();

            const OLD_DATE : HTMLLIElement|null = CONTAINER.querySelector('li#is-selected');
            if (SELECT_CHARACTER) {
                chosen_character = SELECT_CHARACTER.value;
            }
            chosen_number_of_result_per_page = SELECT_NUMBER_OF_RESULT_PER_PAGE.value;

            const BODY: FormData = new FormData();

            if (OLD_DATE) {
                BODY.set("date", chosen_date);
                BODY.set("character", chosen_character);
            }

            BODY.set("numberOfResultPerPage", chosen_number_of_result_per_page);

            const RAID_LIST: HTMLElement = await update_raid_list(BODY);
            stored_raid = [];
            stored_raid[0] = RAID_LIST;
        }
        catch (error)
        {
            console.log(error);
        }
        finally
        {
            clear_process_queue();
        }
    }

    async function change_page_of_result(target: HTMLButtonElement|null): Promise<void>
    {
        try
        {
            clear_process_queue();

            chosen_number_of_page = target.dataset.page;
            chosen_number_of_result_per_page = SELECT_NUMBER_OF_RESULT_PER_PAGE.value;

            if (typeof stored_raid[parseInt(chosen_number_of_page)] === 'undefined')
            {
                const BODY: FormData = new FormData();

                if (chosen_date ) {
                    BODY.set("date", chosen_date);
                    if (chosen_character) {
                        BODY.set("character", chosen_character);
                    }
                }

                BODY.set("numberOfResultPerPage", chosen_number_of_result_per_page);
                BODY.set("currentPage", chosen_number_of_page);

                const RAID_LIST: HTMLElement|null = await update_raid_list(BODY);
                stored_raid[chosen_number_of_page] = RAID_LIST;

            } else {
                const OLD_RAID_LIST: HTMLElement|null = RAID_CONTAINER.querySelector("raid-list");
                OLD_RAID_LIST.insertAdjacentElement("beforebegin", stored_raid[parseInt(chosen_number_of_page)]);
                OLD_RAID_LIST.remove();

                current_page.classList.remove("btn-info", "current");
                current_page.classList.add("btn-primary");

                target.classList.remove("btn-primary");
                target.classList.add("btn-info", "current");

                current_page = target;
            }
        }
        catch (error)
        {
            console.log(error.message);
        }
        finally
        {
            clear_process_queue();
        }
    }

    async function update_raid_list(BODY: FormData)
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
            const BUTTONS: HTMLElement|null = DIV.querySelector("button-list");

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

            const OLD_BUTTON_PAGE_LIST: HTMLElement|null = BUTTON_PAGE_CONTAINER.querySelector("button-list");
            OLD_BUTTON_PAGE_LIST.insertAdjacentElement("beforebegin", BUTTONS);
            OLD_BUTTON_PAGE_LIST.remove();

            current_page = BUTTONS.querySelector("[data-page='"+ chosen_number_of_page +"']")

            return ITEM;
        }
        catch (error)
        {
            console.log(error);
        }
        finally {
            clear_process_queue();
        }
    }

    async function update_calendar(BODY: FormData)
    {
        try {
            clear_process_queue();
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

            return ITEM;
        }
        catch (error)
        {
            console.log(error);
        }
        finally {
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
                change_month(BUTTON.classList.contains("next"));
            }

            const CELL: HTMLLIElement|null = TARGET.closest("li[data-date]:not(.text-secondary):not(#is-selected)") as HTMLLIElement|null;

            if (CELL)
            {
                select_date(CELL);
            }
        }
    );

    BUTTON_PAGE_CONTAINER.addEventListener(
        "click",
        (event: MouseEvent): void =>
        {
            const TARGET: HTMLElement = event.target as HTMLElement;
            const BUTTON: HTMLButtonElement|null = TARGET.closest("button:not(.current)");

            if (BUTTON)
            {
                change_page_of_result(BUTTON);
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

    // Initialize the raid list
    {
        const BUTTON_FIRST_PAGE: HTMLButtonElement = BUTTON_PAGE_CONTAINER.querySelector("[data-page='0']");
        if (BUTTON_FIRST_PAGE) {
            current_page = BUTTON_FIRST_PAGE;
            change_page_of_result(BUTTON_FIRST_PAGE);
        } else {
            change_number_of_result_per_page();
        }
    }
}
