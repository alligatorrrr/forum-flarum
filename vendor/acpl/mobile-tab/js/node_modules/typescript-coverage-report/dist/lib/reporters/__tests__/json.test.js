"use strict";
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
        while (g && (g = 0, op[0] && (_ = 0)), _) try {
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
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var json_1 = require("../json");
var fs_1 = __importDefault(require("fs"));
var path_1 = __importDefault(require("path"));
var fsMocked = fs_1.default.promises;
jest.mock("fs", function () { return ({
    promises: {
        writeFile: jest.fn().mockResolvedValue(undefined)
    }
}); });
describe("generate function", function () {
    var coverageData = {
        fileCounts: new Map([
            ["file1.ts", { correctCount: 10, totalCount: 15 }],
            ["file2.ts", { correctCount: 5, totalCount: 5 }]
        ]),
        anys: [{ file: "file1.ts", character: 1, kind: 1, line: 2, text: "test" }],
        percentage: 75,
        total: 20,
        covered: 15,
        uncovered: 5
    };
    var options = {
        outputDir: "./coverage",
        threshold: 80
    };
    it("writes the correct file with serialized coverage data", function () { return __awaiter(void 0, void 0, void 0, function () {
        var expectedFilePath;
        return __generator(this, function (_a) {
            switch (_a.label) {
                case 0: return [4 /*yield*/, (0, json_1.generate)(coverageData, options)];
                case 1:
                    _a.sent();
                    expectedFilePath = path_1.default.join(options.outputDir, "typescript-coverage.json");
                    expect(fsMocked.writeFile).toHaveBeenCalledWith(expectedFilePath, expect.any(String));
                    expect(JSON.parse(fsMocked.writeFile.mock.calls[0][1].toString())).toMatchObject({
                        fileCounts: {
                            "file1.ts": {
                                correctCount: expect.any(Number),
                                totalCount: expect.any(Number)
                            },
                            "file2.ts": expect.any(Object)
                        },
                        anys: [
                            {
                                file: expect.any(String),
                                character: expect.any(Number),
                                kind: expect.any(Number),
                                line: expect.any(Number),
                                text: expect.any(String)
                            }
                        ],
                        percentage: expect.any(Number),
                        total: expect.any(Number),
                        covered: expect.any(Number),
                        uncovered: expect.any(Number)
                    });
                    return [2 /*return*/];
            }
        });
    }); });
});
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoianNvbi50ZXN0LmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXMiOlsiLi4vLi4vLi4vLi4vc3JjL2xpYi9yZXBvcnRlcnMvX190ZXN0c19fL2pzb24udGVzdC50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQUFBLGdDQUFtQztBQUNuQywwQ0FBb0I7QUFDcEIsOENBQXdCO0FBR3hCLElBQU0sUUFBUSxHQUFHLFlBQUUsQ0FBQyxRQUEyQyxDQUFDO0FBQ2hFLElBQUksQ0FBQyxJQUFJLENBQUMsSUFBSSxFQUFFLGNBQU0sT0FBQSxDQUFDO0lBQ3JCLFFBQVEsRUFBRTtRQUNSLFNBQVMsRUFBRSxJQUFJLENBQUMsRUFBRSxFQUFFLENBQUMsaUJBQWlCLENBQUMsU0FBUyxDQUFDO0tBQ2xEO0NBQ0YsQ0FBQyxFQUpvQixDQUlwQixDQUFDLENBQUM7QUFFSixRQUFRLENBQUMsbUJBQW1CLEVBQUU7SUFDNUIsSUFBTSxZQUFZLEdBQWlCO1FBQ2pDLFVBQVUsRUFBRSxJQUFJLEdBQUcsQ0FBQztZQUNsQixDQUFDLFVBQVUsRUFBRSxFQUFFLFlBQVksRUFBRSxFQUFFLEVBQUUsVUFBVSxFQUFFLEVBQUUsRUFBRSxDQUFDO1lBQ2xELENBQUMsVUFBVSxFQUFFLEVBQUUsWUFBWSxFQUFFLENBQUMsRUFBRSxVQUFVLEVBQUUsQ0FBQyxFQUFFLENBQUM7U0FDakQsQ0FBQztRQUNGLElBQUksRUFBRSxDQUFDLEVBQUUsSUFBSSxFQUFFLFVBQVUsRUFBRSxTQUFTLEVBQUUsQ0FBQyxFQUFFLElBQUksRUFBRSxDQUFDLEVBQUUsSUFBSSxFQUFFLENBQUMsRUFBRSxJQUFJLEVBQUUsTUFBTSxFQUFFLENBQUM7UUFDMUUsVUFBVSxFQUFFLEVBQUU7UUFDZCxLQUFLLEVBQUUsRUFBRTtRQUNULE9BQU8sRUFBRSxFQUFFO1FBQ1gsU0FBUyxFQUFFLENBQUM7S0FDYixDQUFDO0lBRUYsSUFBTSxPQUFPLEdBQUc7UUFDZCxTQUFTLEVBQUUsWUFBWTtRQUN2QixTQUFTLEVBQUUsRUFBRTtLQUNkLENBQUM7SUFFRixFQUFFLENBQUMsdURBQXVELEVBQUU7Ozs7d0JBQzFELHFCQUFNLElBQUEsZUFBUSxFQUFDLFlBQVksRUFBRSxPQUFPLENBQUMsRUFBQTs7b0JBQXJDLFNBQXFDLENBQUM7b0JBRWhDLGdCQUFnQixHQUFHLGNBQUksQ0FBQyxJQUFJLENBQ2hDLE9BQU8sQ0FBQyxTQUFTLEVBQ2pCLDBCQUEwQixDQUMzQixDQUFDO29CQUNGLE1BQU0sQ0FBQyxRQUFRLENBQUMsU0FBUyxDQUFDLENBQUMsb0JBQW9CLENBQzdDLGdCQUFnQixFQUNoQixNQUFNLENBQUMsR0FBRyxDQUFDLE1BQU0sQ0FBQyxDQUNuQixDQUFDO29CQUVGLE1BQU0sQ0FDSixJQUFJLENBQUMsS0FBSyxDQUFDLFFBQVEsQ0FBQyxTQUFTLENBQUMsSUFBSSxDQUFDLEtBQUssQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxRQUFRLEVBQUUsQ0FBQyxDQUMzRCxDQUFDLGFBQWEsQ0FBQzt3QkFDZCxVQUFVLEVBQUU7NEJBQ1YsVUFBVSxFQUFFO2dDQUNWLFlBQVksRUFBRSxNQUFNLENBQUMsR0FBRyxDQUFDLE1BQU0sQ0FBQztnQ0FDaEMsVUFBVSxFQUFFLE1BQU0sQ0FBQyxHQUFHLENBQUMsTUFBTSxDQUFDOzZCQUMvQjs0QkFDRCxVQUFVLEVBQUUsTUFBTSxDQUFDLEdBQUcsQ0FBQyxNQUFNLENBQUM7eUJBQy9CO3dCQUNELElBQUksRUFBRTs0QkFDSjtnQ0FDRSxJQUFJLEVBQUUsTUFBTSxDQUFDLEdBQUcsQ0FBQyxNQUFNLENBQUM7Z0NBQ3hCLFNBQVMsRUFBRSxNQUFNLENBQUMsR0FBRyxDQUFDLE1BQU0sQ0FBQztnQ0FDN0IsSUFBSSxFQUFFLE1BQU0sQ0FBQyxHQUFHLENBQUMsTUFBTSxDQUFDO2dDQUN4QixJQUFJLEVBQUUsTUFBTSxDQUFDLEdBQUcsQ0FBQyxNQUFNLENBQUM7Z0NBQ3hCLElBQUksRUFBRSxNQUFNLENBQUMsR0FBRyxDQUFDLE1BQU0sQ0FBQzs2QkFDekI7eUJBQ0Y7d0JBQ0QsVUFBVSxFQUFFLE1BQU0sQ0FBQyxHQUFHLENBQUMsTUFBTSxDQUFDO3dCQUM5QixLQUFLLEVBQUUsTUFBTSxDQUFDLEdBQUcsQ0FBQyxNQUFNLENBQUM7d0JBQ3pCLE9BQU8sRUFBRSxNQUFNLENBQUMsR0FBRyxDQUFDLE1BQU0sQ0FBQzt3QkFDM0IsU0FBUyxFQUFFLE1BQU0sQ0FBQyxHQUFHLENBQUMsTUFBTSxDQUFDO3FCQUM5QixDQUFDLENBQUM7Ozs7U0FDSixDQUFDLENBQUM7QUFDTCxDQUFDLENBQUMsQ0FBQyJ9