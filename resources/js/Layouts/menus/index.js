import accounting from "./accounting";
import internet from "./internet";
import issues from "./issues";

export default function getModuleMenu(moduleName) {
  return modules[moduleName];
}

const modules = { accounting, internet, issues };
