{
	const CONTAINER: HTMLElement|null = document.querySelector("calendar-wrapper");

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

	let selected_dates: number = 0;
	let min_end_date: string = "";
	let max_end_date: string = "";
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

	async function select_date(target: HTMLLIElement): Promise<void>
	{
		try
		{
			clear_process_queue();

			const DATE_IDENTIFIER: string|undefined = target.dataset.date;

			if (!DATE_IDENTIFIER)
			{
				throw new Error("Missing attribute");
			}

			if (selected_dates === 0)
			{
				if (target.matches(".is-notavailable, .disabled-begin, .disabled-period"))
				{
					return;
				}

				// Arrival date
				min_end_date = DATE_IDENTIFIER;
				target.classList.add("is-selected", "start-date");
				selected_dates = 1;
			}
			else if (selected_dates === 1)
			{
				let found: boolean = false;

				STORED_MONTHS.forEach(
					(widget: HTMLElement): void =>
					{
						if (found)
						{
							return;
						}

						(widget.querySelectorAll("li[data-date].disabled-period, li[data-date].disabled-end") as NodeListOf<HTMLLIElement>).forEach(
							(day: HTMLLIElement): void =>
							{
								if (found)
								{
									return;
								}

								const DATE_IDENTIFIER: string|undefined = day.dataset.date;

								if (DATE_IDENTIFIER && DATE_IDENTIFIER > min_end_date)
								{
									found = true;
									max_end_date = DATE_IDENTIFIER;
								}
							}
						);
					}
				);

				// Departure date
				if (
					DATE_IDENTIFIER > min_end_date
					&&
					(
						!max_end_date
						||
						DATE_IDENTIFIER < max_end_date
					)
				)
				{
					target.classList.add("is-selected", "end-date");

					let is_between: boolean = false;

					STORED_MONTHS.forEach(
						(widget) =>
						{
							widget.querySelectorAll("li[data-date]:not(.disabled-period)").forEach(
								(item) =>
								{
									if (item.classList.contains("is-selected"))
									{
										item.classList.remove("period-select");
										is_between = !is_between;
									}
									else
									{
										item.classList.toggle("period-select", is_between);
									}
								}
							);
						}
					);

					selected_dates = 2;
				}
			}
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

	function reset_all(): void
	{
		try
		{
			clear_process_queue();

			selected_dates = 0;
			min_end_date = "";
			max_end_date = "";

			STORED_MONTHS.forEach(
				(widget: HTMLElement): void =>
				{
					(widget.querySelectorAll("li.is-selected, li.period-select") as NodeListOf<HTMLLIElement>).forEach(
						(item: HTMLLIElement): void =>
						{
							item.classList.remove("is-selected", "period-select");
						}
					);
				}
			);
		}
		catch (error)
		{
			console.log(error);
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
			else if (last_shown_month > 1)
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
						// @TODO : Vérifier la route
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

				STORED_MONTHS[last_shown_month - 2].remove();
				STORED_MONTHS[last_shown_month - 1].insertAdjacentElement("afterend", STORED_MONTHS[last_shown_month]);
			}
			else
			{
				STORED_MONTHS[last_shown_month + 1].remove();
				STORED_MONTHS[last_shown_month].insertAdjacentElement("beforebegin", STORED_MONTHS[last_shown_month - 1]);
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

			const CELL: HTMLLIElement|null = TARGET.closest("li[data-date]:not(.disabled-period):not(.is-notavailable)") as HTMLLIElement|null;

			if (CELL)
			{
				select_date(CELL);
			}

			const RESET_BUTTON: HTMLButtonElement|null = TARGET.closest("button.clear") as HTMLButtonElement|null;

			if (RESET_BUTTON)
			{
				reset_all();
				localStorage.removeItem("form-booking");
			}
		}
	);

	reset_all();

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
					if (!STORED_MONTHS[i].querySelector("li.is-selected"))
					{
						reset_all();

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