"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var react_1 = __importDefault(require("react"));
var path_1 = __importDefault(require("path"));
var semantic_ui_react_1 = require("semantic-ui-react");
var headers = ["Filename", "Percent", "Total", "Covered", "Uncovered"];
var SummaryPage = function (_a) {
    var fileCounts = _a.fileCounts, percentage = _a.percentage, total = _a.total, covered = _a.covered, uncovered = _a.uncovered, threshold = _a.threshold;
    var isSummaryValid = percentage >= threshold;
    return (react_1.default.createElement(semantic_ui_react_1.Container, { style: { marginTop: "3em" } },
        react_1.default.createElement(semantic_ui_react_1.Header, { as: "h1" }, "TypeScript coverage report"),
        react_1.default.createElement(semantic_ui_react_1.Header, { as: "h2" }, "Summary"),
        react_1.default.createElement(semantic_ui_react_1.Table, { celled: true },
            react_1.default.createElement(semantic_ui_react_1.Table.Header, null,
                react_1.default.createElement(semantic_ui_react_1.Table.Row, null, ["Percent", "Threshold", "Total", "Covered", "Uncovered"].map(function (header, index) { return (react_1.default.createElement(semantic_ui_react_1.Table.HeaderCell, { key: index }, header)); }))),
            react_1.default.createElement(semantic_ui_react_1.Table.Body, null,
                react_1.default.createElement(semantic_ui_react_1.Table.Row, { negative: !isSummaryValid, positive: isSummaryValid },
                    react_1.default.createElement(semantic_ui_react_1.Table.Cell, null, percentage.toFixed(2) + "%"),
                    react_1.default.createElement(semantic_ui_react_1.Table.Cell, null,
                        threshold,
                        "%"),
                    react_1.default.createElement(semantic_ui_react_1.Table.Cell, null, total),
                    react_1.default.createElement(semantic_ui_react_1.Table.Cell, null, covered),
                    react_1.default.createElement(semantic_ui_react_1.Table.Cell, null, uncovered)))),
        react_1.default.createElement(semantic_ui_react_1.Header, { as: "h2" }, "Files"),
        react_1.default.createElement(semantic_ui_react_1.Table, { celled: true, style: { marginTop: "2em" }, className: "sortable" },
            react_1.default.createElement(semantic_ui_react_1.Table.Header, null,
                react_1.default.createElement(semantic_ui_react_1.Table.Row, null, headers.map(function (header, index) { return (react_1.default.createElement(semantic_ui_react_1.Table.HeaderCell, { key: index }, header)); }))),
            react_1.default.createElement(semantic_ui_react_1.Table.Body, null, Array.from(fileCounts).map(function (_a, index) {
                var filename = _a[0], _b = _a[1], correctCount = _b.correctCount, totalCount = _b.totalCount;
                var percentage = totalCount === 0 ? 100 : (correctCount * 100) / totalCount;
                var percentageCoverage = percentage.toFixed(2) + "%";
                var isValid = percentage >= threshold;
                return (react_1.default.createElement(semantic_ui_react_1.Table.Row, { key: index, negative: !isValid, positive: isValid },
                    react_1.default.createElement(semantic_ui_react_1.Table.Cell, { selectable: true },
                        react_1.default.createElement("a", { style: { color: "inherit" }, href: path_1.default.join("files", "".concat(filename, ".html")) }, filename)),
                    react_1.default.createElement(semantic_ui_react_1.Table.Cell, null, percentageCoverage),
                    react_1.default.createElement(semantic_ui_react_1.Table.Cell, null, totalCount),
                    react_1.default.createElement(semantic_ui_react_1.Table.Cell, null, correctCount),
                    react_1.default.createElement(semantic_ui_react_1.Table.Cell, null, totalCount - correctCount)));
            })))));
};
exports.default = SummaryPage;
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiU3VtbWFyeVBhZ2UuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyIuLi8uLi8uLi9zcmMvY29tcG9uZW50cy9TdW1tYXJ5UGFnZS9TdW1tYXJ5UGFnZS50c3giXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6Ijs7Ozs7QUFBQSxnREFBMEI7QUFDMUIsOENBQXdCO0FBQ3hCLHVEQUE2RDtBQUc3RCxJQUFNLE9BQU8sR0FBRyxDQUFDLFVBQVUsRUFBRSxTQUFTLEVBQUUsT0FBTyxFQUFFLFNBQVMsRUFBRSxXQUFXLENBQUMsQ0FBQztBQU16RSxJQUFNLFdBQVcsR0FBRyxVQUFDLEVBT2I7UUFOTixVQUFVLGdCQUFBLEVBQ1YsVUFBVSxnQkFBQSxFQUNWLEtBQUssV0FBQSxFQUNMLE9BQU8sYUFBQSxFQUNQLFNBQVMsZUFBQSxFQUNULFNBQVMsZUFBQTtJQUVULElBQU0sY0FBYyxHQUFHLFVBQVUsSUFBSSxTQUFTLENBQUM7SUFFL0MsT0FBTyxDQUNMLDhCQUFDLDZCQUFTLElBQUMsS0FBSyxFQUFFLEVBQUUsU0FBUyxFQUFFLEtBQUssRUFBRTtRQUNwQyw4QkFBQywwQkFBTSxJQUFDLEVBQUUsRUFBQyxJQUFJLGlDQUFvQztRQUNuRCw4QkFBQywwQkFBTSxJQUFDLEVBQUUsRUFBQyxJQUFJLGNBQWlCO1FBQ2hDLDhCQUFDLHlCQUFLLElBQUMsTUFBTTtZQUNYLDhCQUFDLHlCQUFLLENBQUMsTUFBTTtnQkFDWCw4QkFBQyx5QkFBSyxDQUFDLEdBQUcsUUFDUCxDQUFDLFNBQVMsRUFBRSxXQUFXLEVBQUUsT0FBTyxFQUFFLFNBQVMsRUFBRSxXQUFXLENBQUMsQ0FBQyxHQUFHLENBQzVELFVBQUMsTUFBTSxFQUFFLEtBQUssSUFBSyxPQUFBLENBQ2pCLDhCQUFDLHlCQUFLLENBQUMsVUFBVSxJQUFDLEdBQUcsRUFBRSxLQUFLLElBQUcsTUFBTSxDQUFvQixDQUMxRCxFQUZrQixDQUVsQixDQUNGLENBQ1MsQ0FDQztZQUNmLDhCQUFDLHlCQUFLLENBQUMsSUFBSTtnQkFDVCw4QkFBQyx5QkFBSyxDQUFDLEdBQUcsSUFBQyxRQUFRLEVBQUUsQ0FBQyxjQUFjLEVBQUUsUUFBUSxFQUFFLGNBQWM7b0JBQzVELDhCQUFDLHlCQUFLLENBQUMsSUFBSSxRQUFFLFVBQVUsQ0FBQyxPQUFPLENBQUMsQ0FBQyxDQUFDLEdBQUcsR0FBRyxDQUFjO29CQUN0RCw4QkFBQyx5QkFBSyxDQUFDLElBQUk7d0JBQUUsU0FBUzs0QkFBZTtvQkFDckMsOEJBQUMseUJBQUssQ0FBQyxJQUFJLFFBQUUsS0FBSyxDQUFjO29CQUNoQyw4QkFBQyx5QkFBSyxDQUFDLElBQUksUUFBRSxPQUFPLENBQWM7b0JBQ2xDLDhCQUFDLHlCQUFLLENBQUMsSUFBSSxRQUFFLFNBQVMsQ0FBYyxDQUMxQixDQUNELENBQ1A7UUFDUiw4QkFBQywwQkFBTSxJQUFDLEVBQUUsRUFBQyxJQUFJLFlBQWU7UUFDOUIsOEJBQUMseUJBQUssSUFBQyxNQUFNLFFBQUMsS0FBSyxFQUFFLEVBQUUsU0FBUyxFQUFFLEtBQUssRUFBRSxFQUFFLFNBQVMsRUFBQyxVQUFVO1lBQzdELDhCQUFDLHlCQUFLLENBQUMsTUFBTTtnQkFDWCw4QkFBQyx5QkFBSyxDQUFDLEdBQUcsUUFDUCxPQUFPLENBQUMsR0FBRyxDQUFDLFVBQUMsTUFBTSxFQUFFLEtBQUssSUFBSyxPQUFBLENBQzlCLDhCQUFDLHlCQUFLLENBQUMsVUFBVSxJQUFDLEdBQUcsRUFBRSxLQUFLLElBQUcsTUFBTSxDQUFvQixDQUMxRCxFQUYrQixDQUUvQixDQUFDLENBQ1EsQ0FDQztZQUNmLDhCQUFDLHlCQUFLLENBQUMsSUFBSSxRQUNSLEtBQUssQ0FBQyxJQUFJLENBQUMsVUFBVSxDQUFDLENBQUMsR0FBRyxDQUN6QixVQUFDLEVBQXdDLEVBQUUsS0FBSztvQkFBOUMsUUFBUSxRQUFBLEVBQUUsVUFBNEIsRUFBMUIsWUFBWSxrQkFBQSxFQUFFLFVBQVUsZ0JBQUE7Z0JBQ3BDLElBQU0sVUFBVSxHQUNkLFVBQVUsS0FBSyxDQUFDLENBQUMsQ0FBQyxDQUFDLEdBQUcsQ0FBQyxDQUFDLENBQUMsQ0FBQyxZQUFZLEdBQUcsR0FBRyxDQUFDLEdBQUcsVUFBVSxDQUFDO2dCQUM3RCxJQUFNLGtCQUFrQixHQUFHLFVBQVUsQ0FBQyxPQUFPLENBQUMsQ0FBQyxDQUFDLEdBQUcsR0FBRyxDQUFDO2dCQUN2RCxJQUFNLE9BQU8sR0FBRyxVQUFVLElBQUksU0FBUyxDQUFDO2dCQUV4QyxPQUFPLENBQ0wsOEJBQUMseUJBQUssQ0FBQyxHQUFHLElBQUMsR0FBRyxFQUFFLEtBQUssRUFBRSxRQUFRLEVBQUUsQ0FBQyxPQUFPLEVBQUUsUUFBUSxFQUFFLE9BQU87b0JBQzFELDhCQUFDLHlCQUFLLENBQUMsSUFBSSxJQUFDLFVBQVU7d0JBQ3BCLHFDQUNFLEtBQUssRUFBRSxFQUFFLEtBQUssRUFBRSxTQUFTLEVBQUUsRUFDM0IsSUFBSSxFQUFFLGNBQUksQ0FBQyxJQUFJLENBQUMsT0FBTyxFQUFFLFVBQUcsUUFBUSxVQUFPLENBQUMsSUFFM0MsUUFBUSxDQUNQLENBQ087b0JBQ2IsOEJBQUMseUJBQUssQ0FBQyxJQUFJLFFBQUUsa0JBQWtCLENBQWM7b0JBQzdDLDhCQUFDLHlCQUFLLENBQUMsSUFBSSxRQUFFLFVBQVUsQ0FBYztvQkFDckMsOEJBQUMseUJBQUssQ0FBQyxJQUFJLFFBQUUsWUFBWSxDQUFjO29CQUN2Qyw4QkFBQyx5QkFBSyxDQUFDLElBQUksUUFBRSxVQUFVLEdBQUcsWUFBWSxDQUFjLENBQzFDLENBQ2IsQ0FBQztZQUNKLENBQUMsQ0FDRixDQUNVLENBQ1AsQ0FDRSxDQUNiLENBQUM7QUFDSixDQUFDLENBQUM7QUFFRixrQkFBZSxXQUFXLENBQUMifQ==