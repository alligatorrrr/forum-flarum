"use strict";
var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.generate = void 0;
var cli_table3_1 = __importDefault(require("cli-table3"));
var chalk_1 = __importDefault(require("chalk"));
var coverageTable = new cli_table3_1.default({
    chars: { mid: "", "left-mid": "", "mid-mid": "", "right-mid": "" },
    colAligns: ["left", "right", "right", "right", "right"],
    style: { "padding-left": 1, "padding-right": 1 }
});
var calculatePercantage = function (correct, total) {
    if (total === 0) {
        return 100;
    }
    return (correct * 100) / total;
};
var calculatePercantageWithString = function (correct, total) {
    return "".concat(calculatePercantage(correct, total).toFixed(2), "%");
};
var generate = function (_a, threshold) {
    var fileCounts = _a.fileCounts, percentage = _a.percentage, total = _a.total, covered = _a.covered, uncovered = _a.uncovered;
    var headers = [
        "filenames" + chalk_1.default.gray(" (".concat(fileCounts.size, ")")),
        "percent" + chalk_1.default.gray(" (".concat(percentage.toFixed(2), "%)")),
        "total" + chalk_1.default.gray(" (".concat(total, ")")),
        "covered" + chalk_1.default.gray(" (".concat(covered, ")")),
        "uncovered" + chalk_1.default.gray(" (".concat(uncovered, ")"))
    ];
    coverageTable.push(headers, headers.map(function () { return chalk_1.default.gray("---"); }));
    fileCounts.forEach(function (_a, filename) {
        var totalCount = _a.totalCount, correctCount = _a.correctCount;
        var colorCell = function (cell) {
            var color = Math.floor(calculatePercantage(correctCount, totalCount)) >= threshold
                ? chalk_1.default.green
                : chalk_1.default.red;
            if (typeof cell === "object" && "content" in cell) {
                return __assign(__assign({}, cell), { content: color(cell.content) });
            }
            return color(cell);
        };
        coverageTable.push([
            filename,
            calculatePercantageWithString(correctCount, totalCount),
            totalCount,
            correctCount,
            totalCount - correctCount
        ].map(function (val) { return colorCell(val); }));
    });
    return "" + coverageTable;
};
exports.generate = generate;
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoidGV4dC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbIi4uLy4uLy4uL3NyYy9saWIvcmVwb3J0ZXJzL3RleHQudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7Ozs7Ozs7Ozs7QUFBQSwwREFBeUM7QUFFekMsZ0RBQTBCO0FBRTFCLElBQU0sYUFBYSxHQUFHLElBQUksb0JBQUssQ0FBQztJQUM5QixLQUFLLEVBQUUsRUFBRSxHQUFHLEVBQUUsRUFBRSxFQUFFLFVBQVUsRUFBRSxFQUFFLEVBQUUsU0FBUyxFQUFFLEVBQUUsRUFBRSxXQUFXLEVBQUUsRUFBRSxFQUFFO0lBQ2xFLFNBQVMsRUFBRSxDQUFDLE1BQU0sRUFBRSxPQUFPLEVBQUUsT0FBTyxFQUFFLE9BQU8sRUFBRSxPQUFPLENBQUM7SUFDdkQsS0FBSyxFQUFFLEVBQUUsY0FBYyxFQUFFLENBQUMsRUFBRSxlQUFlLEVBQUUsQ0FBQyxFQUFFO0NBQ2pELENBQUMsQ0FBQztBQUVILElBQU0sbUJBQW1CLEdBQUcsVUFBQyxPQUFlLEVBQUUsS0FBYTtJQUN6RCxJQUFJLEtBQUssS0FBSyxDQUFDLEVBQUU7UUFDZixPQUFPLEdBQUcsQ0FBQztLQUNaO0lBRUQsT0FBTyxDQUFDLE9BQU8sR0FBRyxHQUFHLENBQUMsR0FBRyxLQUFLLENBQUM7QUFDakMsQ0FBQyxDQUFDO0FBRUYsSUFBTSw2QkFBNkIsR0FBRyxVQUNwQyxPQUFlLEVBQ2YsS0FBYTtJQUViLE9BQU8sVUFBRyxtQkFBbUIsQ0FBQyxPQUFPLEVBQUUsS0FBSyxDQUFDLENBQUMsT0FBTyxDQUFDLENBQUMsQ0FBQyxNQUFHLENBQUM7QUFDOUQsQ0FBQyxDQUFDO0FBRUssSUFBTSxRQUFRLEdBQUcsVUFDdEIsRUFBbUUsRUFDbkUsU0FBaUI7UUFEZixVQUFVLGdCQUFBLEVBQUUsVUFBVSxnQkFBQSxFQUFFLEtBQUssV0FBQSxFQUFFLE9BQU8sYUFBQSxFQUFFLFNBQVMsZUFBQTtJQUduRCxJQUFNLE9BQU8sR0FBRztRQUNkLFdBQVcsR0FBRyxlQUFLLENBQUMsSUFBSSxDQUFDLFlBQUssVUFBVSxDQUFDLElBQUksTUFBRyxDQUFDO1FBQ2pELFNBQVMsR0FBRyxlQUFLLENBQUMsSUFBSSxDQUFDLFlBQUssVUFBVSxDQUFDLE9BQU8sQ0FBQyxDQUFDLENBQUMsT0FBSSxDQUFDO1FBQ3RELE9BQU8sR0FBRyxlQUFLLENBQUMsSUFBSSxDQUFDLFlBQUssS0FBSyxNQUFHLENBQUM7UUFDbkMsU0FBUyxHQUFHLGVBQUssQ0FBQyxJQUFJLENBQUMsWUFBSyxPQUFPLE1BQUcsQ0FBQztRQUN2QyxXQUFXLEdBQUcsZUFBSyxDQUFDLElBQUksQ0FBQyxZQUFLLFNBQVMsTUFBRyxDQUFDO0tBQzVDLENBQUM7SUFFRixhQUFhLENBQUMsSUFBSSxDQUNoQixPQUFPLEVBQ1AsT0FBTyxDQUFDLEdBQUcsQ0FBQyxjQUFNLE9BQUEsZUFBSyxDQUFDLElBQUksQ0FBQyxLQUFLLENBQUMsRUFBakIsQ0FBaUIsQ0FBQyxDQUNyQyxDQUFDO0lBRUYsVUFBVSxDQUFDLE9BQU8sQ0FDaEIsVUFDRSxFQUcrQyxFQUMvQyxRQUFnQjtZQUhkLFVBQVUsZ0JBQUEsRUFDVixZQUFZLGtCQUFBO1FBSWQsSUFBTSxTQUFTLEdBQUcsVUFBQyxJQUFVO1lBQzNCLElBQU0sS0FBSyxHQUNULElBQUksQ0FBQyxLQUFLLENBQUMsbUJBQW1CLENBQUMsWUFBWSxFQUFFLFVBQVUsQ0FBQyxDQUFDLElBQUksU0FBUztnQkFDcEUsQ0FBQyxDQUFDLGVBQUssQ0FBQyxLQUFLO2dCQUNiLENBQUMsQ0FBQyxlQUFLLENBQUMsR0FBRyxDQUFDO1lBQ2hCLElBQUksT0FBTyxJQUFJLEtBQUssUUFBUSxJQUFJLFNBQVMsSUFBSSxJQUFJLEVBQUU7Z0JBQ2pELDZCQUFZLElBQUksS0FBRSxPQUFPLEVBQUUsS0FBSyxDQUFDLElBQUksQ0FBQyxPQUFPLENBQUMsSUFBRzthQUNsRDtZQUVELE9BQU8sS0FBSyxDQUFDLElBQUksQ0FBQyxDQUFDO1FBQ3JCLENBQUMsQ0FBQztRQUVGLGFBQWEsQ0FBQyxJQUFJLENBQ2hCO1lBQ0UsUUFBUTtZQUNSLDZCQUE2QixDQUFDLFlBQVksRUFBRSxVQUFVLENBQUM7WUFDdkQsVUFBVTtZQUNWLFlBQVk7WUFDWixVQUFVLEdBQUcsWUFBWTtTQUMxQixDQUFDLEdBQUcsQ0FBQyxVQUFDLEdBQUcsSUFBSyxPQUFBLFNBQVMsQ0FBQyxHQUFHLENBQUMsRUFBZCxDQUFjLENBQUMsQ0FDL0IsQ0FBQztJQUNKLENBQUMsQ0FDRixDQUFDO0lBRUYsT0FBTyxFQUFFLEdBQUcsYUFBYSxDQUFDO0FBQzVCLENBQUMsQ0FBQztBQWxEVyxRQUFBLFFBQVEsWUFrRG5CIn0=