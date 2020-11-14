export default {
  name: "issues",
  label: "Issues",
  html: "<span> IC</span><span>Issues</span>",
  headerMenu: [],
  menu: [
    {
      label: "Home",
      to: "/",
      icon: "home"
    },
    {
      label: "Dashboard",
      to: "/dashboard",
      icon: "home",
      childs: [
        {
          icon: "handshake",
          label: "Internet",
          to: "/dashboard/"
        },
        {
          icon: "handshake",
          label: "Ecommerce2",
          to: "/ecommerce2/"
        }
      ]
    },
    {
      label: "Business",
      to: "",
      icon: "handshake",
      childs: [
        { icon: "users", label: "Clients", to: "/clients/" },
        { icon: "briefcase", label: "Services", to: "/services" }
      ]
    },
    {
      label: "Money In",
      icon: "balance-scale-left",
      childs: [{ icon: "handshake", label: "Invoices", to: "/invoices/" }]
    },
    {
      label: "Money Out",
      icon: "balance-scale-right",
      childs: [{ icon: "handshake", label: "Expenses", to: "/expenses/" }]
    },
    {
      label: "accounting",
      icon: "balance-scale",
      childs: [
        { icon: "landmark", label: "Accounts", to: "/accounts/" },
        { icon: "percentage", label: "Taxes", to: "/taxes/" },
        {
          icon: "balance-scale",
          label: "Transactions",
          to: "/transactions/"
        },
        { icon: "receipt", label: "Payments", to: "/payments" }
      ]
    },
    {
      label: "Internet",
      icon: "server",
      childs: [
        { icon: "server", label: "Networks", to: "/networks/" },
        { icon: "money-check-alt", label: "Contracts", to: "/contracts/" }
      ]
    },
    {
      label: "Issues",
      icon: "clock",
      childs: [
        { icon: "clock", label: "Timer", to: "/time-tracker" },
        { icon: "tag", label: "Labels", to: "/labels" },
        { icon: "comments", label: "Milestones", to: "/milestones" },
        { icon: "ticket-alt", label: "Tickets", to: "/tickets" }
      ]
    },
    {
      label: "Reports",
      icon: "home",
      to: "/reports"
    }
  ]
};
