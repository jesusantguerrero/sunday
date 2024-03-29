export default {
  name: "accounting",
  label: "Admin",
  html: "<span>IC </span><span>Admin</span>",
  headerMenu: [
      {
          label: "Help",
          to: "/help",
          icon:"question"
      },
      {
          label: "Info",
          to: "/about",
          icon:"info"
      },
      {
          label: "Settings",
          to: "/user/profile",
          icon:"cogs",
          side: [
           {
               label: "User",
               to: "/user/profile",
               icon: "user"
           },
           {
                label: "Api Tokens",
                to: "/user/api-tokens",
                icon: "user"
            },
        //    {
        //         label: "Billing",
        //         to: "/user/billing",
        //         icon: "user"
        //     },
        //     {
        //         label: "Plans",
        //         to: "/admin/plans",
        //         icon: "money"
        //     },
          ]
      }
  ],
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
    {
        divider:""
    },
    // {
    //     label: "Notes",
    //     icon: "lightbulb",
    //     to: "/notes"
    // },
    // {
    //     label: "Okrs",
    //     icon: "lightbulb",
    //     to: "/okrs"
    // },
    {
        label: "Reports",
        icon: "chart-line",
        to: "/reports"
    },
  ]
};
