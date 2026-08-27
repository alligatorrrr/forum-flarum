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
describe("DetailPage component", function () {
    it("renders correctly", function () {
        var container = (0, react_2.render)(react_1.default.createElement(__1.default, { totalCount: 100, correctCount: 100, threshold: 90, annotations: [], filename: "index.ts", sourceCode: "<p>Test</p>" })).container;
        expect(container.firstChild).toMatchSnapshot();
    });
});
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiRGV0YWlsUGFnZS50ZXN0LmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXMiOlsiLi4vLi4vLi4vLi4vc3JjL2NvbXBvbmVudHMvRGV0YWlsUGFnZS9fX3Rlc3RzX18vRGV0YWlsUGFnZS50ZXN0LnRzeCJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiO0FBQUE7O0dBRUc7Ozs7O0FBRUgsZ0RBQTBCO0FBQzFCLDBDQUE2QjtBQUM3QixnREFBZ0Q7QUFFaEQsUUFBUSxDQUFDLHNCQUFzQixFQUFFO0lBQy9CLEVBQUUsQ0FBQyxtQkFBbUIsRUFBRTtRQUNkLElBQUEsU0FBUyxHQUFLLElBQUEsY0FBTSxFQUMxQiw4QkFBQyxXQUFVLElBQ1QsVUFBVSxFQUFFLEdBQUcsRUFDZixZQUFZLEVBQUUsR0FBRyxFQUNqQixTQUFTLEVBQUUsRUFBRSxFQUNiLFdBQVcsRUFBRSxFQUFFLEVBQ2YsUUFBUSxFQUFDLFVBQVUsRUFDbkIsVUFBVSxFQUFDLGFBQWEsR0FDeEIsQ0FDSCxVQVRnQixDQVNmO1FBRUYsTUFBTSxDQUFDLFNBQVMsQ0FBQyxVQUFVLENBQUMsQ0FBQyxlQUFlLEVBQUUsQ0FBQztJQUNqRCxDQUFDLENBQUMsQ0FBQztBQUNMLENBQUMsQ0FBQyxDQUFDIn0=