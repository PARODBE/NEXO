'use strict';
// Comprueba el configurador y el flujo ilustrativo sin navegador ni servicios externos.
const fs=require('node:fs');
const vm=require('node:vm');
const assert=require('node:assert/strict');
function element(){return {children:[],dataset:{},textContent:'',disabled:false,checked:false,classList:{toggle(){}},append(...items){this.children.push(...items);},replaceChildren(...items){this.children=[...items];},setAttribute(name,value){this[name]=value;},addEventListener(event,handler){this[event]=handler;}};}
const form=element(),list=element(),hidden=element(),status=element(),save=element();
const plan={...element(),selectedOptions:[{dataset:{limit:'10'}}]};
const picks=[1,2,3,4,5,6].map(id=>({...element(),value:String(id),closest(){return {classList:{toggle(){}}};}}));
const nodes={'#team-form':form,'#team-plan':plan,'#selected-list':list,'#selected-inputs':hidden,'#selection-status':status,'#save-team':save,'#builder-data':{textContent:JSON.stringify({agents:[1,2,3,4,5,6].map(id=>({id,name:'Agente '+id})),selected:[3,1]})}};
const context={document:{querySelector:selector=>nodes[selector]||null,querySelectorAll:selector=>selector==='.agent-pick'?picks:[],createElement:element}};
vm.createContext(context);vm.runInContext(fs.readFileSync('assets/js/builder.js','utf8'),context);
const ordered=()=>hidden.children.map(input=>Number(input.value));
assert.deepEqual(ordered(),[3,1]);
assert.equal(list.children[0].children[1].children[0].disabled,true);
picks[1].checked=true;picks[1].change();picks[3].checked=true;picks[3].change();
assert.deepEqual(ordered(),[3,1,2,4]);
list.children[0].children[1].children[1].click();
assert.deepEqual(ordered(),[1,3,2,4]);
plan.selectedOptions[0].dataset.limit='3';plan.change();
assert.equal(save.disabled,true);assert.equal(picks[4].disabled,true);
let blocked=false;form.submit({preventDefault(){blocked=true;}});assert.equal(blocked,true);
list.children[1].children[1].children[2].click();
assert.deepEqual(ordered(),[1,2,4]);assert.equal(save.disabled,false);
picks[1].checked=true;picks[1].change();assert.deepEqual(ordered(),[1,2,4]);

const controls=['campaign','service','project'].map(workflow=>({...element(),dataset:{workflow}}));
const workflowNodes=Object.fromEntries(['#workflow-objective','#workflow-output',...Array.from({length:3},(_,i)=>'#workflow-name-'+i),...Array.from({length:3},(_,i)=>'#workflow-task-'+i)].map(id=>[id,element()]));
let networkCalls=0;
const workflowContext={document:{querySelector:selector=>workflowNodes[selector]||null,querySelectorAll:selector=>selector==='[data-workflow]'?controls:[]},fetch(){networkCalls++;throw new Error('Un flujo ilustrativo no debe realizar peticiones.');}};
vm.createContext(workflowContext);vm.runInContext(fs.readFileSync('assets/js/app.js','utf8'),workflowContext);
controls[2].click();
assert.equal(workflowNodes['#workflow-objective'].textContent,'ORGANIZAR UN PROYECTO');
assert.equal(workflowNodes['#workflow-name-1'].textContent,'Coordinador');
assert.equal(controls[2]['aria-pressed'],'true');assert.equal(controls[0]['aria-pressed'],'false');
controls[0].click();assert.equal(workflowNodes['#workflow-output'].textContent,'Una propuesta lista para revisar');
assert.equal(networkCalls,0);
console.log('OK: orden y movimiento, selección sin duplicados, cambio de límite, bloqueo de guardado y flujo ilustrativo sin peticiones.');
