import{r,t as i}from"./index.590f4998.js";function e(t){r(1,arguments);var a=i(t);return a.setHours(0,0,0,0),a}function u(t,a){r(2,arguments);var n=e(t),s=e(a);return n.getTime()===s.getTime()}function f(t){return r(1,arguments),u(t,Date.now())}export{f as i};