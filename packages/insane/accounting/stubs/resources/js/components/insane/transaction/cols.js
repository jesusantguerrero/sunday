import Vue from "vue";
const vue = new Vue();

export default [
  {
    label: "Concept",
    name: "concept",
    type: "custom",
    fixed: "true"
  },
  {
    label: "Account",
    name: "account",
    width: "300",
    type: "custom",
    cellClass: "overflow-y-visible"
  },
  {
    label: "Debit",
    name: "debit",
    width: "120",
    type: "custom",
    class: "text-success",
    onInput(row) {
      row.credit = 0;
    }
  },
  {
    label: "Credit",
    name: "credit",
    width: "120",
    type: "custom",
    class: "text-danger",
    onInput(e, row) {
      row.debit = 0;
    }
  },
  {
    label: "",
    name: "actions",
    width: "50",
    type: "custom",
    class: "no-print",
    headerClass: "no-print"
  }
];
