var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
var __generator = (this && this.__generator) || function (thisArg, body) {
    var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
    return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
    function verb(n) { return function (v) { return step([n, v]); }; }
    function step(op) {
        if (f) throw new TypeError("Generator is already executing.");
        while (_) try {
            if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
            if (y = 0, t) op = [op[0] & 2, t.value];
            switch (op[0]) {
                case 0: case 1: t = op; break;
                case 4: _.label++; return { value: op[1], done: false };
                case 5: _.label++; y = op[1]; op = [0]; continue;
                case 7: op = _.ops.pop(); _.trys.pop(); continue;
                default:
                    if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                    if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                    if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                    if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                    if (t[2]) _.ops.pop();
                    _.trys.pop(); continue;
            }
            op = body.call(thisArg, _);
        } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
        if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
    }
};
{
    // Container
    var CONTAINER_1 = document.querySelector("calendar-wrapper");
    var RAID_CONTAINER_1 = document.querySelector("raid-wrapper");
    var BUTTON_LIST_CONTAINER = document.querySelector("button-list");
    // Select list
    var SELECT_CHARACTER_1 = document.querySelector("#raid_character_userCharacter");
    var SELECT_NUMBER_OF_RESULT_PER_PAGE_1 = document.querySelector("#nbrOfResultPerPage");
    // Calendar
    var NOW_1 = new Date();
    var STORED_MONTHS_1 = Array.from(CONTAINER_1.querySelectorAll("widget-calendar"));
    var last_shown_month_1 = STORED_MONTHS_1.length - 1;
    var abort_handle_1 = null;
    // Raid List
    var stored_raid_1 = [];
    var chosen_number_of_result_per_page_1 = "";
    // Filters for raid list
    var chosen_date_1 = "";
    var chosen_character_1 = "";
    var chosen_number_of_page_1 = "0";
    if (CONTAINER_1 === null) {
        throw new Error("Missing calendar wrapper");
    }
    if (STORED_MONTHS_1.length < 1) {
        throw new Error("Missing elements");
    }
    function clear_process_queue() {
        if (abort_handle_1) {
            abort_handle_1.abort();
            abort_handle_1 = null;
        }
    }
    function change_character() {
        return __awaiter(this, void 0, Promise, function () {
            var BODY, RAID_LIST, error_1;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        _a.trys.push([0, 2, 3, 4]);
                        clear_process_queue();
                        chosen_character_1 = SELECT_CHARACTER_1.value;
                        if (!chosen_character_1) {
                            throw new Error("Missing Attribute");
                        }
                        chosen_number_of_result_per_page_1 = SELECT_NUMBER_OF_RESULT_PER_PAGE_1.value;
                        BODY = new FormData();
                        BODY.set("character", chosen_character_1);
                        BODY.set("date", chosen_date_1);
                        BODY.set("numberOfResultPerPage", chosen_number_of_result_per_page_1);
                        return [4 /*yield*/, update_raid_list(BODY)];
                    case 1:
                        RAID_LIST = _a.sent();
                        stored_raid_1 = [];
                        stored_raid_1[0] = RAID_LIST;
                        return [3 /*break*/, 4];
                    case 2:
                        error_1 = _a.sent();
                        console.log(error_1);
                        return [3 /*break*/, 4];
                    case 3:
                        clear_process_queue();
                        return [7 /*endfinally*/];
                    case 4: return [2 /*return*/];
                }
            });
        });
    }
    function change_month(go_forward) {
        return __awaiter(this, void 0, Promise, function () {
            var MONTH, BODY, ITEM, error_2;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        _a.trys.push([0, 5, 6, 7]);
                        clear_process_queue();
                        if (go_forward) {
                            ++last_shown_month_1;
                        }
                        else if (last_shown_month_1 >= 1) {
                            --last_shown_month_1;
                        }
                        else {
                            return [2 /*return*/];
                        }
                        if (!go_forward) return [3 /*break*/, 3];
                        if (!!STORED_MONTHS_1[last_shown_month_1]) return [3 /*break*/, 2];
                        MONTH = new Date(NOW_1.getFullYear(), NOW_1.getMonth() + last_shown_month_1, 27, 0, 0, 0, 0);
                        BODY = new FormData();
                        BODY.set("date", MONTH.toISOString().substr(0, 10));
                        return [4 /*yield*/, update_calendar(BODY)];
                    case 1:
                        ITEM = _a.sent();
                        if (ITEM) {
                            STORED_MONTHS_1.push(ITEM);
                        }
                        _a.label = 2;
                    case 2:
                        STORED_MONTHS_1[last_shown_month_1 - 1].insertAdjacentElement("afterend", STORED_MONTHS_1[last_shown_month_1]);
                        STORED_MONTHS_1[last_shown_month_1 - 1].remove();
                        return [3 /*break*/, 4];
                    case 3:
                        STORED_MONTHS_1[last_shown_month_1 + 1].insertAdjacentElement("beforebegin", STORED_MONTHS_1[last_shown_month_1]);
                        STORED_MONTHS_1[last_shown_month_1 + 1].remove();
                        _a.label = 4;
                    case 4: return [3 /*break*/, 7];
                    case 5:
                        error_2 = _a.sent();
                        console.log(error_2);
                        if (go_forward) {
                            --last_shown_month_1;
                        }
                        else {
                            ++last_shown_month_1;
                        }
                        return [3 /*break*/, 7];
                    case 6:
                        clear_process_queue();
                        return [7 /*endfinally*/];
                    case 7: return [2 /*return*/];
                }
            });
        });
    }
    function select_date(target) {
        if (target === void 0) { target = null; }
        return __awaiter(this, void 0, Promise, function () {
            var DATE_IDENTIFIER, OLD_DATE, BODY, RAID_LIST, error_3;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        _a.trys.push([0, 2, 3, 4]);
                        clear_process_queue();
                        DATE_IDENTIFIER = void 0;
                        DATE_IDENTIFIER = target.dataset.date;
                        if (!DATE_IDENTIFIER) {
                            throw new Error("Missing Attribute");
                        }
                        if (target.matches(".is-notavailable")) {
                            return [2 /*return*/];
                        }
                        OLD_DATE = CONTAINER_1.querySelector('li#is-selected');
                        if (OLD_DATE) {
                            OLD_DATE.removeAttribute('id');
                        }
                        target.id = 'is-selected';
                        chosen_date_1 = DATE_IDENTIFIER;
                        chosen_number_of_result_per_page_1 = SELECT_NUMBER_OF_RESULT_PER_PAGE_1.value;
                        if (SELECT_CHARACTER_1) {
                            chosen_character_1 = SELECT_CHARACTER_1.value;
                        }
                        BODY = new FormData();
                        BODY.set("date", chosen_date_1);
                        BODY.set("character", chosen_character_1);
                        BODY.set("numberOfResultPerPage", chosen_number_of_result_per_page_1);
                        return [4 /*yield*/, update_raid_list(BODY)];
                    case 1:
                        RAID_LIST = _a.sent();
                        stored_raid_1 = [];
                        stored_raid_1[0] = RAID_LIST;
                        return [3 /*break*/, 4];
                    case 2:
                        error_3 = _a.sent();
                        console.log(error_3);
                        return [3 /*break*/, 4];
                    case 3:
                        clear_process_queue();
                        return [7 /*endfinally*/];
                    case 4: return [2 /*return*/];
                }
            });
        });
    }
    function change_number_of_result_per_page() {
        return __awaiter(this, void 0, Promise, function () {
            var OLD_DATE, BODY, RAID_LIST, error_4;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        _a.trys.push([0, 2, 3, 4]);
                        clear_process_queue();
                        OLD_DATE = CONTAINER_1.querySelector('li#is-selected');
                        if (SELECT_CHARACTER_1) {
                            chosen_character_1 = SELECT_CHARACTER_1.value;
                        }
                        chosen_number_of_result_per_page_1 = SELECT_NUMBER_OF_RESULT_PER_PAGE_1.value;
                        BODY = new FormData();
                        if (OLD_DATE) {
                            BODY.set("date", chosen_date_1);
                            BODY.set("character", chosen_character_1);
                        }
                        if (SELECT_CHARACTER_1) {
                            BODY.set("character", chosen_character_1);
                        }
                        BODY.set("numberOfResultPerPage", chosen_number_of_result_per_page_1);
                        return [4 /*yield*/, update_raid_list(BODY)];
                    case 1:
                        RAID_LIST = _a.sent();
                        stored_raid_1 = [];
                        stored_raid_1[0] = RAID_LIST;
                        return [3 /*break*/, 4];
                    case 2:
                        error_4 = _a.sent();
                        console.log(error_4);
                        return [3 /*break*/, 4];
                    case 3:
                        clear_process_queue();
                        return [7 /*endfinally*/];
                    case 4: return [2 /*return*/];
                }
            });
        });
    }
    function change_page_of_result(target) {
        return __awaiter(this, void 0, Promise, function () {
            var BODY, RAID_LIST, OLD_RAID_LIST, error_5;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        _a.trys.push([0, 4, 5, 6]);
                        clear_process_queue();
                        chosen_number_of_page_1 = target.dataset.page;
                        chosen_number_of_result_per_page_1 = SELECT_NUMBER_OF_RESULT_PER_PAGE_1.value;
                        if (!(typeof stored_raid_1[parseInt(chosen_number_of_page_1)] === 'undefined')) return [3 /*break*/, 2];
                        BODY = new FormData();
                        if (chosen_date_1) {
                            BODY.set("date", chosen_date_1);
                        }
                        if (SELECT_CHARACTER_1) {
                            BODY.set("character", SELECT_CHARACTER_1.value);
                        }
                        BODY.set("numberOfResultPerPage", chosen_number_of_result_per_page_1);
                        BODY.set("currentPage", chosen_number_of_page_1);
                        return [4 /*yield*/, update_raid_list(BODY)];
                    case 1:
                        RAID_LIST = _a.sent();
                        if (RAID_LIST) {
                            stored_raid_1[chosen_number_of_page_1] = RAID_LIST;
                        }
                        return [3 /*break*/, 3];
                    case 2:
                        OLD_RAID_LIST = RAID_CONTAINER_1.querySelector("raid-list");
                        OLD_RAID_LIST.insertAdjacentElement("beforebegin", stored_raid_1[parseInt(chosen_number_of_page_1)]);
                        OLD_RAID_LIST.remove();
                        _a.label = 3;
                    case 3: return [3 /*break*/, 6];
                    case 4:
                        error_5 = _a.sent();
                        console.log(error_5);
                        return [3 /*break*/, 6];
                    case 5:
                        clear_process_queue();
                        return [7 /*endfinally*/];
                    case 6: return [2 /*return*/];
                }
            });
        });
    }
    function update_raid_list(BODY) {
        return __awaiter(this, void 0, void 0, function () {
            var RESPONSE, HTML, DIV, ITEM, TITLE, BUTTONS, OLD_RAID_LIST, OLD_RAID_LIST_TITLE, error_6;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        _a.trys.push([0, 3, , 4]);
                        abort_handle_1 = new AbortController();
                        return [4 /*yield*/, fetch("/ajax/get-all-raid-of-the-day", {
                                method: "POST",
                                body: BODY,
                                signal: abort_handle_1.signal
                            })];
                    case 1:
                        RESPONSE = _a.sent();
                        if (!RESPONSE.ok) {
                            throw new Error(RESPONSE.statusText);
                        }
                        return [4 /*yield*/, RESPONSE.text()];
                    case 2:
                        HTML = _a.sent();
                        DIV = document.createElement("div");
                        DIV.innerHTML = HTML;
                        ITEM = DIV.querySelector("raid-list");
                        TITLE = DIV.querySelector("raid-list-title");
                        BUTTONS = DIV.querySelector("button-list");
                        if (!ITEM) {
                            throw new Error("Invalid response");
                        }
                        OLD_RAID_LIST = RAID_CONTAINER_1.querySelector("raid-list");
                        OLD_RAID_LIST.insertAdjacentElement("beforebegin", ITEM);
                        OLD_RAID_LIST.remove();
                        OLD_RAID_LIST_TITLE = RAID_CONTAINER_1.querySelector("raid-list-title");
                        OLD_RAID_LIST_TITLE.insertAdjacentElement("beforebegin", TITLE);
                        OLD_RAID_LIST_TITLE.remove();
                        return [2 /*return*/, ITEM];
                    case 3:
                        error_6 = _a.sent();
                        console.log(error_6);
                        return [3 /*break*/, 4];
                    case 4: return [2 /*return*/];
                }
            });
        });
    }
    function update_calendar(BODY) {
        return __awaiter(this, void 0, void 0, function () {
            var RESPONSE, HTML, DIV, ITEM, error_7;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        _a.trys.push([0, 3, , 4]);
                        abort_handle_1 = new AbortController();
                        return [4 /*yield*/, fetch("/ajax/get-availability-calendar", {
                                method: "POST",
                                body: BODY,
                                signal: abort_handle_1.signal
                            })];
                    case 1:
                        RESPONSE = _a.sent();
                        if (!RESPONSE.ok) {
                            throw new Error(RESPONSE.statusText);
                        }
                        return [4 /*yield*/, RESPONSE.text()];
                    case 2:
                        HTML = _a.sent();
                        DIV = document.createElement("div");
                        DIV.innerHTML = HTML;
                        ITEM = DIV.querySelector("widget-calendar");
                        if (!ITEM) {
                            throw new Error("Invalid response");
                        }
                        return [2 /*return*/, ITEM];
                    case 3:
                        error_7 = _a.sent();
                        console.log(error_7);
                        return [3 /*break*/, 4];
                    case 4: return [2 /*return*/];
                }
            });
        });
    }
    CONTAINER_1.addEventListener("click", function (event) {
        var TARGET = event.target;
        var BUTTON = TARGET.closest("button.next, button.prev");
        if (BUTTON) {
            change_month(BUTTON.classList.contains("next"));
        }
        var CELL = TARGET.closest("li[data-date]:not(.text-secondary):not(#is-selected)");
        if (CELL) {
            select_date(CELL);
        }
    });
    RAID_CONTAINER_1.addEventListener("click", function (event) {
        var TARGET = event.target;
        var BUTTON = TARGET.closest("button-list button:not(.current)");
        if (BUTTON) {
            change_page_of_result(BUTTON);
        }
    });
    SELECT_NUMBER_OF_RESULT_PER_PAGE_1.addEventListener("change", function (event) {
        var resultPerPage = [10, 20, 50, 70, 100];
        var resultPerPageSelected = parseInt(SELECT_NUMBER_OF_RESULT_PER_PAGE_1.value);
        if (resultPerPage.includes(resultPerPageSelected)) {
            change_number_of_result_per_page();
        }
    });
    SELECT_CHARACTER_1 ? SELECT_CHARACTER_1.addEventListener("change", function () {
        change_character();
    }) : null;
    // Initialize the raid list
    {
        var BUTTON_FIRST_PAGE = BUTTON_LIST_CONTAINER.querySelector("[data-page='0']");
        if (BUTTON_FIRST_PAGE) {
            change_page_of_result(BUTTON_FIRST_PAGE);
        }
        else {
            change_number_of_result_per_page();
        }
    }
}
