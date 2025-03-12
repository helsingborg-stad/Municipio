/** @type {import('ts-jest').JestConfigWithTsJest} **/
module.exports = {
  testEnvironment: "node",
  transform: {
    "^.+\.tsx?$": ["ts-jest", {}]
  },
  moduleNameMapper: {
    "^!!raw-loader!.*": "jest-raw-loader",
  },
};