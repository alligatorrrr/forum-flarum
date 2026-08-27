#!/usr/bin/env node
"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var commander_1 = require("commander");
var lib_1 = __importDefault(require("../lib"));
var path_1 = __importDefault(require("path"));
var _a = require("../../package.json"), version = _a.version, description = _a.description;
var _b = require(path_1.default.join(process.cwd(), "/package.json")).typeCoverage, typeCoverage = _b === void 0 ? {} : _b;
var argvWithVersion = function (argvs) {
    var vPos = argvs.indexOf("-v");
    if (vPos > -1) {
        argvs[vPos] = "-V";
    }
    return argvs;
};
var _c = typeCoverage.outputDir, outputDir = _c === void 0 ? "coverage-ts" : _c, _d = typeCoverage.atLeast, atLeast = _d === void 0 ? 80 : _d, _e = typeCoverage.strict, strict = _e === void 0 ? false : _e, _f = typeCoverage.debug, debug = _f === void 0 ? false : _f, _g = typeCoverage.cache, cache = _g === void 0 ? false : _g, _h = typeCoverage.project, project = _h === void 0 ? "." : _h, _j = typeCoverage.ignoreFiles, ignoreFiles = _j === void 0 ? false : _j, _k = typeCoverage.ignoreCatch, ignoreCatch = _k === void 0 ? false : _k, _l = typeCoverage.ignoreUnread, ignoreUnread = _l === void 0 ? false : _l;
commander_1.program
    .version(version)
    .description(description)
    .option("-o, --outputDir [string]", "the output directory where to generate the report.", outputDir)
    .option("-t, --threshold [number]", "the minimum percentage of coverage required.", parseFloat, atLeast)
    .option("-s, --strict [boolean]", "run the check in strict mode.", strict)
    .option("-d, --debug [boolean]", "show debug information.", debug)
    .option("-c, --cache [boolean]", "save and reuse type check result from cache.", cache)
    .option("-p, --project [string]", 'file path to tsconfig file, eg: --project "./app/tsconfig.app.json"', project)
    .option("-i, --ignore-files [string[]]", 'ignore specified files, eg: --ignore-files "demo1/*.ts" --ignore-files "demo2/foo.ts"', ignoreFiles)
    .option("--ignore-catch [boolean]", "ignore type any for (try-)catch clause variable", ignoreCatch)
    .option("-u, --ignore-unread [boolean]", "allow writes to variables with implicit any types", ignoreUnread)
    .option("-- [string[]]", "only checks these files, useful for usage with tools like lint-staged")
    .parse(argvWithVersion(process.argv));
var options = {
    /* camelCase keys matching "long" flags in options above */
    outputDir: commander_1.program.outputDir,
    threshold: commander_1.program.threshold,
    tsProjectFile: commander_1.program.project,
    strict: commander_1.program.strict,
    debug: commander_1.program.debug,
    cache: commander_1.program.cache,
    ignoreFiles: commander_1.program.ignoreFiles,
    ignoreCatch: commander_1.program.ignoreCatch,
    ignoreUnread: commander_1.program.ignoreUnread,
    files: commander_1.program.args.length > 0 ? commander_1.program.args : undefined
};
(0, lib_1.default)(options)
    .then(function (_a) {
    var percentage = _a.percentage;
    if (percentage < options.threshold) {
        console.error("\nThe TypeScript coverage ".concat(percentage.toFixed(2), "% is lower than the threshold ").concat(options.threshold, "%"));
        process.exit(2);
    }
    process.exit(0);
})
    .catch(function (error) {
    console.error(error);
    process.exit(255);
});
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoidHlwZXNjcmlwdC1jb3ZlcmFnZS1yZXBvcnQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyIuLi8uLi9zcmMvYmluL3R5cGVzY3JpcHQtY292ZXJhZ2UtcmVwb3J0LnRzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiI7Ozs7OztBQUVBLHVDQUFvQztBQUNwQywrQ0FBZ0U7QUFDaEUsOENBQXdCO0FBZWxCLElBQUEsS0FJUyxPQUFPLENBQUMsb0JBQW9CLENBQUMsRUFIMUMsT0FBTyxhQUFBLEVBQ1AsV0FBVyxpQkFFK0IsQ0FBQztBQUczQyxJQUFBLEtBRXFELE9BQU8sQ0FBQyxjQUFJLENBQUMsSUFBSSxDQUN0RSxPQUFPLENBQUMsR0FBRyxFQUFFLEVBQ2IsZUFBZSxDQUNoQixDQUFDLGFBTGlCLEVBQWpCLFlBQVksbUJBQUcsRUFBRSxLQUFBLENBS2hCO0FBRUgsSUFBTSxlQUFlLEdBQUcsVUFBQyxLQUFlO0lBQ3RDLElBQU0sSUFBSSxHQUFHLEtBQUssQ0FBQyxPQUFPLENBQUMsSUFBSSxDQUFDLENBQUM7SUFFakMsSUFBSSxJQUFJLEdBQUcsQ0FBQyxDQUFDLEVBQUU7UUFDYixLQUFLLENBQUMsSUFBSSxDQUFDLEdBQUcsSUFBSSxDQUFDO0tBQ3BCO0lBRUQsT0FBTyxLQUFLLENBQUM7QUFDZixDQUFDLENBQUM7QUFHQSxJQUFBLEtBU0UsWUFBWSxVQVRXLEVBQXpCLFNBQVMsbUJBQUcsYUFBYSxLQUFBLEVBQ3pCLEtBUUUsWUFBWSxRQVJGLEVBQVosT0FBTyxtQkFBRyxFQUFFLEtBQUEsRUFDWixLQU9FLFlBQVksT0FQQSxFQUFkLE1BQU0sbUJBQUcsS0FBSyxLQUFBLEVBQ2QsS0FNRSxZQUFZLE1BTkQsRUFBYixLQUFLLG1CQUFHLEtBQUssS0FBQSxFQUNiLEtBS0UsWUFBWSxNQUxELEVBQWIsS0FBSyxtQkFBRyxLQUFLLEtBQUEsRUFDYixLQUlFLFlBQVksUUFKRCxFQUFiLE9BQU8sbUJBQUcsR0FBRyxLQUFBLEVBQ2IsS0FHRSxZQUFZLFlB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