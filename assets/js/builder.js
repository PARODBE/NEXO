'use strict';
const teamForm=document.querySelector('#team-form');
if(teamForm){
 const data=JSON.parse(document.querySelector('#builder-data').textContent);
 const names=new Map(data.agents.map(agent=>[Number(agent.id),agent.name]));
 let selected=[...data.selected];
 const picks=[...document.querySelectorAll('.agent-pick')];
 const plan=document.querySelector('#team-plan');
 const list=document.querySelector('#selected-list');
 const hidden=document.querySelector('#selected-inputs');
 const status=document.querySelector('#selection-status');
 const save=document.querySelector('#save-team');
 function render(){
   const limit=Number(plan.selectedOptions[0].dataset.limit);list.replaceChildren();hidden.replaceChildren();
   selected.forEach((id,index)=>{
     const row=document.createElement('li');const label=document.createElement('span');label.textContent=names.get(id);row.append(label);
     const controls=document.createElement('div');controls.className='sequence-controls';
     [['↑','Subir',-1],['↓','Bajar',1],['×','Quitar',0]].forEach(([symbol,text,direction])=>{
       const button=document.createElement('button');button.type='button';button.textContent=symbol;button.setAttribute('aria-label',`${text} ${names.get(id)}`);
       button.disabled=(direction===-1&&index===0)||(direction===1&&index===selected.length-1);
       button.addEventListener('click',()=>{if(direction===0)selected=selected.filter(value=>value!==id);else[selected[index],selected[index+direction]]=[selected[index+direction],selected[index]];render();});
       controls.append(button);
     });row.append(controls);list.append(row);
     const input=document.createElement('input');input.type='hidden';input.name='agents[]';input.value=id;hidden.append(input);
   });
   picks.forEach(pick=>{pick.checked=selected.includes(Number(pick.value));pick.disabled=!pick.checked&&selected.length>=limit;pick.closest('.select-agent-card').classList.toggle('is-selected',pick.checked);});
   status.textContent=selected.length?`${selected.length} agentes seleccionados · máximo ${limit} en este plan`:'Elige al menos un agente.';
   status.classList.toggle('error',selected.length>limit);save.disabled=selected.length<1||selected.length>limit;
 }
 picks.forEach(pick=>pick.addEventListener('change',()=>{const id=Number(pick.value);if(pick.checked&&!selected.includes(id))selected.push(id);else if(!pick.checked)selected=selected.filter(value=>value!==id);render();}));
 plan.addEventListener('change',render);
 teamForm.addEventListener('submit',event=>{const limit=Number(plan.selectedOptions[0].dataset.limit);if(!selected.length||selected.length>limit){event.preventDefault();status.textContent=`Selecciona entre 1 y ${limit} agentes.`;}});
 render();
}
