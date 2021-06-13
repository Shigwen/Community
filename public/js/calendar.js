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
var _this = this;
{
    var CONTAINER_1 = document.querySelector("calendar-wrapper");
    var RAID_CONTAINER_1 = document.querySelector("raid-wrapper");
    var SELECT_CHARACTER_1 = document.querySelector("#raid_character_userCharacter");
    if (CONTAINER_1 === null) {
        throw new Error("Missing calendar wrapper");
    }
    var NOW_1 = new Date();
    var STORED_MONTHS_1 = Array.from(CONTAINER_1.querySelectorAll("widget-calendar"));
    if (STORED_MONTHS_1.length < 1) {
        throw new Error("Missing elements");
    }
    var chosen_date_1 = "";
    var chosen_character_1 = "";
    var chosen_role = "";
    var last_shown_month_1 = STORED_MONTHS_1.length - 1;
    var abort_handle_1 = null;
    function clear_process_queue() {
        if (abort_handle_1) {
            abort_handle_1.abort();
            abort_handle_1 = null;
        }
    }
    function select_date(target) {
        if (target === void 0) { target = null; }
        return __awaiter(this, void 0, Promise, function () {
            var DATE_IDENTIFIER, OLD_LI, BODY, RESPONSE, HTML, DIV, ITEM, OLD_RAID_LIST, error_1;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        _a.trys.push([0, 3, 4, 5]);
                        clear_process_queue();
                        DATE_IDENTIFIER = void 0;
                        OLD_LI = CONTAINER_1.querySelector('li#is-selected');
                        if (target) {
                            DATE_IDENTIFIER = target.dataset.date;
                        }
                        else {
                            if (!OLD_LI) {
                                return [2 /*return*/];
                            }
                            DATE_IDENTIFIER = OLD_LI.dataset.date;
                        }
                        if (!DATE_IDENTIFIER) {
                            throw new Error("Missing Attribute");
                        }
                        if (target && target.matches(".is-notavailable")) {
                            return [2 /*return*/];
                        }
                        if (target) {
                            if (OLD_LI) {
                                OLD_LI.removeAttribute("id");
                            }
                            target.id = 'is-selected';
                        }
                        chosen_date_1 = DATE_IDENTIFIER;
                        BODY = new FormData();
                        BODY.set("date", chosen_date_1);
                        if (SELECT_CHARACTER_1) {
                            chosen_character_1 = SELECT_CHARACTER_1.value;
                            BODY.set("character", chosen_character_1);
                        }
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
                        if (!ITEM) {
                            throw new Error("Invalid response");
                        }
                        OLD_RAID_LIST = RAID_CONTAINER_1.querySelector("raid-list");
                        OLD_RAID_LIST.insertAdjacentElement("beforebegin", ITEM);
                        OLD_RAID_LIST.remove();
                        return [3 /*break*/, 5];
                    case 3:
                        error_1 = _a.sent();
                        console.log(error_1);
                        return [3 /*break*/, 5];
                    case 4:
                        clear_process_queue();
                        return [7 /*endfinally*/];
                    case 5: return [2 /*return*/];
                }
            });
        });
    }
    function update_calendar(go_forward) {
        return __awaiter(this, void 0, Promise, function () {
            var MONTH, BODY, RESPONSE, HTML, DIV, ITEM, error_2;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        _a.trys.push([0, 6, 7, 8]);
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
                        if (!go_forward) return [3 /*break*/, 4];
                        if (!!STORED_MONTHS_1[last_shown_month_1]) return [3 /*break*/, 3];
                        MONTH = new Date(NOW_1.getFullYear(), NOW_1.getMonth() + last_shown_month_1, 27, 0, 0, 0, 0);
                        BODY = new FormData();
                        BODY.set("date", MONTH.toISOString().substr(0, 10));
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
                        STORED_MONTHS_1.push(ITEM);
                        _a.label = 3;
                    case 3:
                        STORED_MONTHS_1[last_shown_month_1 - 1].insertAdjacentElement("afterend", STORED_MONTHS_1[last_shown_month_1]);
                        STORED_MONTHS_1[last_shown_month_1 - 1].remove();
                        return [3 /*break*/, 5];
                    case 4:
                        STORED_MONTHS_1[last_shown_month_1 + 1].insertAdjacentElement("beforebegin", STORED_MONTHS_1[last_shown_month_1]);
                        STORED_MONTHS_1[last_shown_month_1 + 1].remove();
                        _a.label = 5;
                    case 5: return [3 /*break*/, 8];
                    case 6:
                        error_2 = _a.sent();
                        console.log(error_2);
                        if (go_forward) {
                            --last_shown_month_1;
                        }
                        else {
                            ++last_shown_month_1;
                        }
                        return [3 /*break*/, 8];
                    case 7:
                        clear_process_queue();
                        return [7 /*endfinally*/];
                    case 8: return [2 /*return*/];
                }
            });
        });
    }
    CONTAINER_1.addEventListener("click", function (event) {
        var TARGET = event.target;
        var BUTTON = TARGET.closest("button.next, button.prev");
        if (BUTTON) {
            update_calendar(BUTTON.classList.contains("next"));
        }
        var CELL = TARGET.closest("li[data-date]:not(.text-secondary):not(#is-selected)");
        if (CELL) {
            select_date(CELL);
        }
    });
    SELECT_CHARACTER_1 ? SELECT_CHARACTER_1.addEventListener("change", function (event) {
        var TARGET = event.target;
        var SELECT = TARGET.closest("select#raid_character_userCharacter, select#raid_character_role");
        if (SELECT) {
            select_date();
        }
    }) : null;
    // Initialize from storage
    {
        var ITEM = localStorage.getItem("form-booking");
        if (ITEM) {
            var DATES_1 = JSON.parse(ITEM);
            if (DATES_1.length === 2 && Date.parse(DATES_1[0]) > Date.now()) {
                (function () { return __awaiter(_this, void 0, Promise, function () {
                    var i, j, ISO_DATE, done, CELL, BASE;
                    return __generator(this, function (_a) {
                        switch (_a.label) {
                            case 0:
                                i = 0;
                                j = 0;
                                _a.label = 1;
                            case 1:
                                if (!(j < 2)) return [3 /*break*/, 9];
                                ISO_DATE = DATES_1[j];
                                done = false;
                                _a.label = 2;
                            case 2:
                                if (!!done) return [3 /*break*/, 8];
                                if (!!STORED_MONTHS_1[i]) return [3 /*break*/, 4];
                                // Load new month
                                return [4 /*yield*/, update_calendar(true)];
                            case 3:
                                // Load new month
                                _a.sent();
                                _a.label = 4;
                            case 4:
                                CELL = STORED_MONTHS_1[i].querySelector("li[data-date=\"" + ISO_DATE + "\"]");
                                if (!CELL) return [3 /*break*/, 6];
                                // Equivalent to a selecting click
                                return [4 /*yield*/, select_date(CELL)];
                            case 5:
                                // Equivalent to a selecting click
                                _a.sent();
                                done = true;
                                return [3 /*break*/, 7];
                            case 6:
                                ++i;
                                _a.label = 7;
                            case 7: return [3 /*break*/, 2];
                            case 8:
                                ++j;
                                return [3 /*break*/, 1];
                            case 9:
                                // Reset if unavailable
                                if (!STORED_MONTHS_1[i].querySelector("li#is-selected")) {
                                    if (i > 0) {
                                        BASE = STORED_MONTHS_1[i].nextElementSibling;
                                        STORED_MONTHS_1[i].remove();
                                        STORED_MONTHS_1[i - 1].remove();
                                        if (BASE) {
                                            BASE.insertAdjacentElement("beforebegin", STORED_MONTHS_1[0]);
                                            BASE.insertAdjacentElement("beforebegin", STORED_MONTHS_1[1]);
                                        }
                                    }
                                }
                                return [2 /*return*/];
                        }
                    });
                }); })();
            }
            else {
                localStorage.removeItem("form-booking");
            }
        }
    }
}
