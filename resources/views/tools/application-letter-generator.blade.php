@extends('layouts.app')

@section('title', 'Application Letter Generator Pakistan - Leave, Job & Bank Applications | Azlaan Tools')
@section('meta_description', 'Free application letter generator for Pakistan. Create leave applications for school and office, job applications, fee concession and bank account letters. Print or copy instantly.')

@section('styles')
<style>
@media print {
    body * { visibility: hidden !important; }
    #printArea, #printArea * { visibility: visible !important; }
    #printArea { position: absolute; left: 0; top: 0; width: 100%; padding: 24px; }
}
#printArea { white-space: pre-wrap; font-family: Georgia, 'Times New Roman', serif; font-size: 1.05rem; line-height: 1.7; }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Application Letter Generator</h1>
            <p class="lead text-muted">Fill the form and get a ready, correctly formatted application — for school, office, bank and more.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="template">Application type</label>
                    <select class="form-select" id="template">
                        <option value="leaveSchool">Leave Application — School</option>
                        <option value="leaveOffice">Leave Application — Office</option>
                        <option value="job">Job Application</option>
                        <option value="principal">Application to the Principal</option>
                        <option value="principalUrdu">Leave Application to Principal — Urdu</option>
                        <option value="bank">Bank Account Opening Request</option>
                        <option value="fee">Fee Concession Application</option>
                    </select>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6"><label class="form-label" for="fName">Your full name</label><input class="form-control fld" id="fName" placeholder="e.g. Muhammad Ahmad"></div>
                        <div class="col-md-6"><label class="form-label" for="fFather">Father's name (optional)</label><input class="form-control fld" id="fFather" placeholder="e.g. Muhammad Iqbal"></div>
                        <div class="col-md-6"><label class="form-label" for="fClass">Class / Designation</label><input class="form-control fld" id="fClass" placeholder="e.g. Class 9-B / Sales Officer"></div>
                        <div class="col-md-6"><label class="form-label" for="fOrg">School / Company / Bank name</label><input class="form-control fld" id="fOrg" placeholder="e.g. Govt High School, Lahore"></div>
                        <div class="col-md-6"><label class="form-label" for="fDateFrom">From date</label><input class="form-control fld" id="fDateFrom" type="date"></div>
                        <div class="col-md-6"><label class="form-label" for="fDateTo">To date</label><input class="form-control fld" id="fDateTo" type="date"></div>
                        <div class="col-md-6"><label class="form-label" for="fAddressee">Addressee (optional)</label><input class="form-control fld" id="fAddressee" placeholder="e.g. The Principal / The Manager"></div>
                        <div class="col-md-6"><label class="form-label" for="fCity">City (optional)</label><input class="form-control fld" id="fCity" placeholder="e.g. Karachi"></div>
                        <div class="col-12"><label class="form-label" for="fReason">Reason / details</label><textarea class="form-control fld" id="fReason" rows="2" placeholder="e.g. I am suffering from fever / a family function at home"></textarea></div>
                    </div>
                    <div class="d-flex gap-2 mt-3 flex-wrap"><button type="button" class="btn btn-primary" id="printBtn">Print Letter</button><button type="button" class="btn btn-success" id="copyBtn">Copy Letter</button></div>
                </div>
            </div>
            <div class="card shadow-sm mb-4"><div class="card-body"><h2 class="h5">Live Preview</h2><div id="printArea"></div></div></div>
            <h2>How to use</h2>
            <ol><li>Choose the application type and fill your details — the letter updates live.</li><li>Check the preview, then Print (only the letter prints) or Copy it.</li><li>Write or paste it neatly wherever you need to submit it, and sign at the end.</li></ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function(){
 function v(id){ var el=document.getElementById(id); return el && el.value.trim() ? el.value.trim() : null; }
 function fmtDate(val){ if(!val) return '________'; var d=new Date(val+'T00:00:00'); return d.toLocaleDateString('en-GB',{day:'numeric',month:'long',year:'numeric'}); }
 function today(){ return new Date().toLocaleDateString('en-GB',{day:'numeric',month:'long',year:'numeric'}); }
 function build(){
   var name=v('fName')||'Your Name';
   var father=v('fFather');
   var sonLine=father?('\nS/o '+father):'';
   var cls=v('fClass')||'________';
   var org=v('fOrg')||'________';
   var reason=v('fReason')||'personal reasons';
   var from=fmtDate(v('fDateFrom')), to=fmtDate(v('fDateTo'));
   var addr=v('fAddressee'), city=v('fCity');
   var t=document.getElementById('template').value;
   var L='';
   if(t==='leaveSchool'){
     L='To,\n'+(addr||'The Principal')+',\n'+org+(city?', '+city:'')+'.\n\nSubject: Application for Leave\n\nRespected Sir/Madam,\n\nWith due respect, I beg to state that I, '+name+', a student of '+cls+', cannot attend school from '+from+' to '+to+' because '+reason+'. Kindly grant me leave for these days. I shall be very thankful to you.\n\nYours obediently,\n'+name+sonLine+'\nClass: '+cls+'\nDate: '+today();
   } else if(t==='leaveOffice'){
     L='To,\n'+(addr||'The Manager')+',\n'+org+(city?', '+city:'')+'.\n\nSubject: Application for Leave\n\nRespected Sir/Madam,\n\nWith due respect, I request leave from '+from+' to '+to+' due to '+reason+'. My pending work will be completed before I leave, and I will remain available on phone for any urgent matter. Kindly approve my leave. I shall be grateful.\n\nYours sincerely,\n'+name+sonLine+'\nDesignation: '+cls+'\nDate: '+today();
   } else if(t==='job'){
     L='To,\n'+(addr||'The Hiring Manager')+',\n'+org+(city?', '+city:'')+'.\n\nSubject: Application for the Post of '+cls+'\n\nRespected Sir/Madam,\n\nWith reference to your job advertisement, I, '+name+', wish to apply for the post of '+cls+' in your esteemed organisation. I have the required education and skills for this position, and I assure you that I will work honestly and with full dedication if given this opportunity. My CV is attached for your kind consideration.\n\nI hope you will give me a chance to appear in an interview.\n\nYours faithfully,\n'+name+sonLine+'\nDate: '+today();
   } else if(t==='principal'){
     L='To,\nThe Principal,\n'+org+(city?', '+city:'')+'.\n\nSubject: Application Regarding '+reason+'\n\nRespected Sir/Madam,\n\nWith due respect, I, '+name+', a student of '+cls+', humbly state that '+reason+'. I request you to kindly look into this matter and grant the necessary permission / relief. I shall be highly obliged for this act of kindness.\n\nYours obediently,\n'+name+sonLine+'\nClass: '+cls+'\nDate: '+today();
   } else if(t==='principalUrdu'){
     L='بخدمت جناب پرنسپل صاحب،\n'+org+'\n\nعنوان: چھٹی کی درخواست\n\nجنابِ عالی!\nمؤدبانہ گزارش ہے کہ میں '+name+'، جماعت '+cls+' کا طالب علم ہوں۔ میں '+from+' سے '+to+' تک اسکول حاضر نہیں ہو سکوں گا کیونکہ '+reason+'۔ ازراہِ کرم مجھے ان دنوں کی چھٹی عنایت فرمائی جائے۔ میں آپ کا بے حد مشکور ہوں گا۔\n\nآپ کا فرمانبردار شاگرد،\n'+name+(father?('\nولد '+father):'')+'\nجماعت: '+cls+'\nتاریخ: '+today();
   } else if(t==='bank'){
     L='To,\n'+(addr||'The Branch Manager')+',\n'+org+(city?', '+city:'')+'.\n\nSubject: Request for Opening a Bank Account\n\nRespected Sir/Madam,\n\nI, '+name+sonLine+', request you to kindly open a savings account in your branch in my name. I am ready to submit all required documents, including my CNIC copy, and to deposit the initial amount as per bank rules. Kindly guide me about the procedure and oblige.\n\nYours faithfully,\n'+name+'\nDate: '+today();
   } else {
     L='To,\n'+(addr||'The Principal')+',\n'+org+(city?', '+city:'')+'.\n\nSubject: Application for Fee Concession\n\nRespected Sir/Madam,\n\nWith due respect, I, '+name+', a student of '+cls+', humbly state that '+reason+', due to which my family is unable to pay my full fee. I am a regular and hardworking student. I request you to kindly grant me fee concession so that I can continue my studies. I shall be very thankful to you.\n\nYours obediently,\n'+name+sonLine+'\nClass: '+cls+'\nDate: '+today();
   }
   var area=document.getElementById('printArea');
   area.textContent=L;
   area.dir=(t==='principalUrdu')?'rtl':'ltr';
   area.style.textAlign=(t==='principalUrdu')?'right':'left';
   return L;
 }
 document.querySelectorAll('.fld, #template').forEach(function(el){ el.addEventListener('input', build); el.addEventListener('change', build); });
 document.getElementById('copyBtn').addEventListener('click', function(){ var txt=build(); if(navigator.clipboard) navigator.clipboard.writeText(txt); });
 document.getElementById('printBtn').addEventListener('click', function(){ build(); window.print(); });
 build();
})();
</script>
@endsection
