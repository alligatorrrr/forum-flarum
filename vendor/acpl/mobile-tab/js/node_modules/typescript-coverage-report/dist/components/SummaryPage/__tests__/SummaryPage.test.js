"use strict";
/**
 * @jest-environment jsdom
 */
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var react_1 = __importDefault(require("react"));
var __1 = __importDefault(require("../"));
var react_2 = require("@testing-library/react");
describe("SummaryPage component", function () {
    it("renders correctly", function () {
        var container = (0, react_2.render)(react_1.default.createElement(__1.default, { fileCounts: new Map([["index.html", { totalCount: 100, correctCount: 100 }]]), percentage: 100, total: 100, covered: 100, uncovered: 0, threshold: 90 })).container;
        expect(container.firstChild).toMatchSnapshot();
    });
});
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiU3VtbWFyeVBhZ2UudGVzdC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbIi4uLy4uLy4uLy4uL3NyYy9jb21wb25lbnRzL1N1bW1hcnlQYWdlL19fdGVzdHNfXy9TdW1tYXJ5UGFnZS50ZXN0LnRzeCJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiO0FBQUE7O0dBRUc7Ozs7O0FBRUgsZ0RBQTBCO0FBQzFCLDBDQUE4QjtBQUM5QixnREFBZ0Q7QUFFaEQsUUFBUSxDQUFDLHVCQUF1QixFQUFFO0lBQ2hDLEVBQUUsQ0FBQyxtQkFBbUIsRUFBRTtRQUNkLElBQUEsU0FBUyxHQUFLLElBQUEsY0FBTSxFQUMxQiw4QkFBQyxXQUFXLElBQ1YsVUFBVSxFQUNSLElBQUksR0FBRyxDQUFDLENBQUMsQ0FBQyxZQUFZLEVBQUUsRUFBRSxVQUFVLEVBQUUsR0FBRyxFQUFFLFlBQVksRUFBRSxHQUFHLEVBQUUsQ0FBQyxDQUFDLENBQUMsRUFFbkUsVUFBVSxFQUFFLEdBQUcsRUFDZixLQUFLLEVBQUUsR0FBRyxFQUNWLE9BQU8sRUFBRSxHQUFHLEVBQ1osU0FBUyxFQUFFLENBQUMsRUFDWixTQUFTLEVBQUUsRUFBRSxHQUNiLENBQ0gsVUFYZ0IsQ0FXZjtRQUVGLE1BQU0sQ0FBQyxTQUFTLENBQUMsVUFBVSxDQUFDLENBQUMsZUFBZSxFQUFFLENBQUM7SUFDakQsQ0FBQyxDQUFDLENBQUM7QUFDTCxDQUFDLENBQUMsQ0FBQyJ9