import daily from "./daily";

export default function getModuleMenu(moduleName) {
  return modules[moduleName];
}

const modules = { daily };
