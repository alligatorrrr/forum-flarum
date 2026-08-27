"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var react_1 = __importDefault(require("react"));
var path_1 = __importDefault(require("path"));
var semantic_ui_react_1 = require("semantic-ui-react");
var headers = [
    "Filename",
    "Percent",
    "Threshold",
    "Total",
    "Covered",
    "Uncovered"
];
var DetailPage = function (_a) {
    var filename = _a.filename, sourceCode = _a.sourceCode, totalCount = _a.totalCount, correctCount = _a.correctCount, annotations = _a.annotations, threshold = _a.threshold;
    var percentage = totalCount === 0 ? 100 : (correctCount * 100) / totalCount;
    var percentageCoverage = percentage.toFixed(2) + "%";
    var isValid = percentage >= threshold;
    return (react_1.default.createElement(semantic_ui_react_1.Container, { style: { marginTop: "3em" } },
        react_1.default.createElement(semantic_ui_react_1.Header, { as: "h1" },
            react_1.default.createElement("a", { href: path_1.default.relative("".concat(filename, ".html"), "index.html") }, "TypeScript coverage report")),
        react_1.default.createElement(semantic_ui_react_1.Table, { celled: true, style: { marginTop: "2em" } },
            react_1.default.createElement(semantic_ui_react_1.Table.Header, null,
                react_1.default.createElement(semantic_ui_react_1.Table.Row, null, headers.map(function (header, index) { return (react_1.default.createElement(semantic_ui_react_1.Table.HeaderCell, { key: index }, header)); }))),
            react_1.default.createElement(semantic_ui_react_1.Table.Body, null,
                react_1.default.createElement(semantic_ui_react_1.Table.Row, { negative: !isValid, positive: isValid },
                    react_1.default.createElement(semantic_ui_react_1.Table.Cell, null, filename),
                    react_1.default.createElement(semantic_ui_react_1.Table.Cell, null, percentageCoverage),
                    react_1.default.createElement(semantic_ui_react_1.Table.Cell, null,
                        threshold,
                        "%"),
                    react_1.default.createElement(semantic_ui_react_1.Table.Cell, null, totalCount),
                    react_1.default.createElement(semantic_ui_react_1.Table.Cell, null, correctCount),
                    react_1.default.createElement(semantic_ui_react_1.Table.Cell, null, totalCount - correctCount)))),
        react_1.default.createElement("textarea", { id: "editor", value: sourceCode, readOnly: true, style: { marginTop: "3em" } }),
        react_1.default.createElement("pre", { id: "annotations", style: { display: "none" } }, JSON.stringify(annotations))));
};
exports.default = DetailPage;
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiRGV0YWlsUGFnZS5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbIi4uLy4uLy4uL3NyYy9jb21wb25lbnRzL0RldGFpbFBhZ2UvRGV0YWlsUGFnZS50c3giXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6Ijs7Ozs7QUFBQSxnREFBMEI7QUFDMUIsOENBQXdCO0FBQ3hCLHVEQUE2RDtBQUU3RCxJQUFNLE9BQU8sR0FBRztJQUNkLFVBQVU7SUFDVixTQUFTO0lBQ1QsV0FBVztJQUNYLE9BQU87SUFDUCxTQUFTO0lBQ1QsV0FBVztDQUNaLENBQUM7QUFrQkYsSUFBTSxVQUFVLEdBQUcsVUFBQyxFQU9aO1FBTk4sUUFBUSxjQUFBLEVBQ1IsVUFBVSxnQkFBQSxFQUNWLFVBQVUsZ0JBQUEsRUFDVixZQUFZLGtCQUFBLEVBQ1osV0FBVyxpQkFBQSxFQUNYLFNBQVMsZUFBQTtJQUVULElBQU0sVUFBVSxHQUFHLFVBQVUsS0FBSyxDQUFDLENBQUMsQ0FBQyxDQUFDLEdBQUcsQ0FBQyxDQUFDLENBQUMsQ0FBQyxZQUFZLEdBQUcsR0FBRyxDQUFDLEdBQUcsVUFBVSxDQUFDO0lBQzlFLElBQU0sa0JBQWtCLEdBQUcsVUFBVSxDQUFDLE9BQU8sQ0FBQyxDQUFDLENBQUMsR0FBRyxHQUFHLENBQUM7SUFDdkQsSUFBTSxPQUFPLEdBQUcsVUFBVSxJQUFJLFNBQVMsQ0FBQztJQUV4QyxPQUFPLENBQ0wsOEJBQUMsNkJBQVMsSUFBQyxLQUFLLEVBQUUsRUFBRSxTQUFTLEVBQUUsS0FBSyxFQUFFO1FBQ3BDLDhCQUFDLDBCQUFNLElBQUMsRUFBRSxFQUFDLElBQUk7WUFDYixxQ0FBRyxJQUFJLEVBQUUsY0FBSSxDQUFDLFFBQVEsQ0FBQyxVQUFHLFFBQVEsVUFBTyxFQUFFLFlBQVksQ0FBQyxpQ0FFcEQsQ0FDRztRQUNULDhCQUFDLHlCQUFLLElBQUMsTUFBTSxRQUFDLEtBQUssRUFBRSxFQUFFLFNBQVMsRUFBRSxLQUFLLEVBQUU7WUFDdkMsOEJBQUMseUJBQUssQ0FBQyxNQUFNO2dCQUNYLDhCQUFDLHlCQUFLLENBQUMsR0FBRyxRQUNQLE9BQU8sQ0FBQyxHQUFHLENBQUMsVUFBQyxNQUFNLEVBQUUsS0FBSyxJQUFLLE9BQUEsQ0FDOUIsOEJBQUMseUJBQUssQ0FBQyxVQUFVLElBQUMsR0FBRyxFQUFFLEtBQUssSUFBRyxNQUFNLENBQW9CLENBQzFELEVBRitCLENBRS9CLENBQUMsQ0FDUSxDQUNDO1lBQ2YsOEJBQUMseUJBQUssQ0FBQyxJQUFJO2dCQUNULDhCQUFDLHlCQUFLLENBQUMsR0FBRyxJQUFDLFFBQVEsRUFBRSxDQUFDLE9BQU8sRUFBRSxRQUFRLEVBQUUsT0FBTztvQkFDOUMsOEJBQUMseUJBQUssQ0FBQyxJQUFJLFFBQUUsUUFBUSxDQUFjO29CQUNuQyw4QkFBQyx5QkFBSyxDQUFDLElBQUksUUFBRSxrQkFBa0IsQ0FBYztvQkFDN0MsOEJBQUMseUJBQUssQ0FBQyxJQUFJO3dCQUFFLFNBQVM7NEJBQWU7b0JBQ3JDLDhCQUFDLHlCQUFLLENBQUMsSUFBSSxRQUFFLFVBQVUsQ0FBYztvQkFDckMsOEJBQUMseUJBQUssQ0FBQyxJQUFJLFFBQUUsWUFBWSxDQUFjO29CQUN2Qyw4QkFBQyx5QkFBSyxDQUFDLElBQUksUUFBRSxVQUFVLEdBQUcsWUFBWSxDQUFjLENBQzFDLENBQ0QsQ0FDUDtRQUNSLDRDQUNFLEVBQUUsRUFBQyxRQUFRLEVBQ1gsS0FBSyxFQUFFLFVBQVUsRUFDakIsUUFBUSxRQUNSLEtBQUssRUFBRSxFQUFFLFNBQVMsRUFBRSxLQUFLLEVBQUUsR0FDM0I7UUFDRix1Q0FBSyxFQUFFLEVBQUMsYUFBYSxFQUFDLEtBQUssRUFBRSxFQUFFLE9BQU8sRUFBRSxNQUFNLEVBQUUsSUFDN0MsSUFBSSxDQUFDLFNBQVMsQ0FBQyxXQUFXLENBQUMsQ0FDeEIsQ0FDSSxDQUNiLENBQUM7QUFDSixDQUFDLENBQUM7QUFFRixrQkFBZSxVQUFVLENBQUMifQ==