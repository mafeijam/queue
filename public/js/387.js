"use strict";(self.webpackChunk=self.webpackChunk||[]).push([[387],{9387:(e,t,o)=>{o.r(t),o.d(t,{default:()=>w});var l=o(5166),c={class:"q-gutter-md"},r=(0,l.createElementVNode)("div",{class:"text-h4 text-teal"},"電子排隊系統",-1),n={class:"flex"},a=(0,l.createElementVNode)("div",{class:"text-h5 text-grey-7 q-mr-lg"},"店舖名稱",-1),i={class:"text-h5 text-indigo-7"},s=(0,l.createElementVNode)("div",{class:"text-h6 q-mb-md"},"已經獲取票號",-1),d={class:"row q-col-gutter-md text-subtitle1"},u=(0,l.createElementVNode)("div",{class:"col-12"},[(0,l.createElementVNode)("div",{class:"text-h5 text-pink"},"請選擇人數並取票")],-1),p={class:"col-12"},m={class:"col-12"},f={class:"col-12"},h={class:"col-12"};var k=o(9680),x=o(9038);const v={props:{shop:Object,ticket:Object},setup:function(e,t){(0,t.expose)();var o=e,c={props:o,getTicket:function(e){return k.Inertia.post("/get_ticket",{type:e,shop:o.shop.id,uuid:o.shop.uuid},{onError:function(e){return console.log(e)}})},ticketOpen:function(e){return window.location=e},computed:l.computed,Inertia:k.Inertia,usePage:x.qt};return Object.defineProperty(c,"__isScriptSetup",{enumerable:!1,value:!0}),c},render:function(e,t,o,k,x,v){var w=(0,l.resolveComponent)("q-card-section"),g=(0,l.resolveComponent)("q-btn"),C=(0,l.resolveComponent)("q-card"),N=(0,l.resolveComponent)("q-page"),V=(0,l.resolveComponent)("q-page-container"),b=(0,l.resolveComponent)("q-layout");return(0,l.openBlock)(),(0,l.createBlock)(b,{view:"hHh lpr fFf"},{default:(0,l.withCtx)((function(){return[(0,l.createVNode)(V,{class:"bg-grey-1"},{default:(0,l.withCtx)((function(){return[(0,l.createVNode)(N,{class:"window-height",style:{"max-width":"800px",margin:"auto"}},{default:(0,l.withCtx)((function(){return[(0,l.createVNode)(C,{class:"shadow-1 window-height",flat:""},{default:(0,l.withCtx)((function(){return[(0,l.createVNode)(w,{class:"q-pa-md"},{default:(0,l.withCtx)((function(){return[(0,l.createElementVNode)("div",c,[r,(0,l.createElementVNode)("div",n,[a,(0,l.createElementVNode)("div",i,(0,l.toDisplayString)(o.shop.name),1)])])]})),_:1}),o.ticket.exists?((0,l.openBlock)(),(0,l.createBlock)(w,{key:0,class:"q-pa-md"},{default:(0,l.withCtx)((function(){return[s,(0,l.createVNode)(g,{label:"查看票號",onClick:t[0]||(t[0]=function(e){return k.ticketOpen(o.ticket.link)}),color:"purple",size:"lg"})]})),_:1})):((0,l.openBlock)(),(0,l.createBlock)(w,{key:1,class:"q-pa-md"},{default:(0,l.withCtx)((function(){return[(0,l.createElementVNode)("div",d,[u,(0,l.createElementVNode)("div",p,[(0,l.createVNode)(g,{class:"full-width",label:"A 1 ~ 2人",color:"red-1","text-color":"red-8",size:"1.8rem",onClick:t[1]||(t[1]=function(e){return k.getTicket("A")})})]),(0,l.createElementVNode)("div",m,[(0,l.createVNode)(g,{class:"full-width",label:"B 3 ~ 4人",color:"green-1","text-color":"green-8",size:"1.8rem",onClick:t[2]||(t[2]=function(e){return k.getTicket("B")})})]),(0,l.createElementVNode)("div",f,[(0,l.createVNode)(g,{class:"full-width",label:"C 5 ~ 6人",color:"blue-1","text-color":"blue-8",size:"1.8rem",onClick:t[3]||(t[3]=function(e){return k.getTicket("C")})})]),(0,l.createElementVNode)("div",h,[(0,l.createVNode)(g,{class:"full-width",label:"D 7人或以上",color:"yellow-1","text-color":"yellow-8",size:"1.8rem",onClick:t[4]||(t[4]=function(e){return k.getTicket("D")})})])])]})),_:1}))]})),_:1})]})),_:1})]})),_:1})]})),_:1})}},w=v}}]);