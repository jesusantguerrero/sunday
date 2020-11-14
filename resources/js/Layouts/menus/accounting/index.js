export default {
  name: "accounting",
  label: "Admin",
  html: "<span>IC </span><span>Admin</span>",
  headerMenu: [],
  menu: [
    {
      label: "Dashboard",
      to: "/",
      icon: "home"
    },
    {
      label: "Planner",
      icon: "calendar-alt",
      to: "/planner"
    },
    {
        label: "Tracker",
        icon: "clock",
        to: "/tracker"
    },
    {
        label: "Integrations",
        icon: "robot",
        to: "/integrations"
    },
  ]
};
