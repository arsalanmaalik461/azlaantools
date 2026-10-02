@extends('layouts.app')

@section('title', 'Text Encrypt & Decrypt Online - AES-256 with Password | Azlaan Tools')
@section('meta_description', 'Free text encryptor. Lock any text with a password using AES-256 encryption in your browser, and decrypt it back. Nothing is sent to any server.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Text Encrypt &amp; Decrypt</h1>
            <p class="lead text-muted">Lock text with a password using strong AES-256 encryption — 100% in your browser.</p>
            <div class="alert alert-danger"><strong>Warning:</strong> If you forget the password, the text can <strong>never</strong> be recovered. Nothing is sent to any server — encryption happens only on your device.</div>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="input">Text (plain text to encrypt, or encrypted code to decrypt)</label>
                    <textarea class="form-control" id="input" rows="6" placeholder="Type secret text here, or paste an encrypted code here..."></textarea>
                    <label class="form-label fw-semibold mt-3" for="pass">Password</label>
                    <input class="form-control" id="pass" type="password" placeholder="Use a long, strong password you will remember" autocomplete="new-password">
                    <div class="d-flex gap-2 mt-3 flex-wrap"><button type="button" class="btn btn-primary" id="encBtn">Encrypt</button><button type="button" class="btn btn-outline-primary" id="decBtn">Decrypt</button></div>
                    <div class="alert mt-3 d-none" id="status"></div>
                    <label class="form-label fw-semibold mt-2" for="output">Result</label>
                    <textarea class="form-control" id="output" rows="6" readonly></textarea>
                    <button type="button" class="btn btn-success btn-sm mt-2" id="copyBtn">Copy Result</button>
                </div>
            </div>
            <h2>How to use</h2>
            <ol><li><strong>To encrypt:</strong> type your text, set a password, click Encrypt, and copy the code. Share the code anywhere — only the password can open it.</li><li><strong>To decrypt:</strong> paste the code, type the same password, click Decrypt.</li><li>Keep the password safe separately — without it, recovery is impossible by design.</li></ol>
            <p class="text-muted small">How it works: PBKDF2 (150,000 rounds) turns your password into a key, then AES-GCM 256 encrypts the text. The random salt and IV are packed with the result in Base64.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function(){
 var input=document.getElementById('input'), pass=document.getElementById('pass'), output=document.getElementById('output'), status=document.getElementById('status');
 function show(msg, ok){ status.textContent=msg; status.className='alert mt-3 '+(ok?'alert-success':'alert-danger'); }
 function b64(bytes){ var s=''; bytes.forEach(function(b){ s+=String.fromCharCode(b); }); return btoa(s); }
 function unb64(str){ var s=atob(str.trim()); var arr=new Uint8Array(s.length); for(var i=0;i<s.length;i++) arr[i]=s.charCodeAt(i); return arr; }
 async function getKey(password, salt){
   var enc=new TextEncoder();
   var base=await crypto.subtle.importKey('raw', enc.encode(password), 'PBKDF2', false, ['deriveKey']);
   return crypto.subtle.deriveKey({name:'PBKDF2', salt:salt, iterations:150000, hash:'SHA-256'}, base, {name:'AES-GCM', length:256}, false, ['encrypt','decrypt']);
 }
 async function encrypt(){
   if(!input.value){ show('Please type some text first.', false); return; }
   if(!pass.value){ show('Please set a password.', false); return; }
   try{
     var salt=crypto.getRandomValues(new Uint8Array(16));
     var iv=crypto.getRandomValues(new Uint8Array(12));
     var key=await getKey(pass.value, salt);
     var ct=await crypto.subtle.encrypt({name:'AES-GCM', iv:iv}, key, new TextEncoder().encode(input.value));
     var packed=new Uint8Array(salt.length+iv.length+ct.byteLength);
     packed.set(salt,0); packed.set(iv,salt.length); packed.set(new Uint8Array(ct), salt.length+iv.length);
     output.value=b64(packed); show('Encrypted successfully. Copy the code and keep your password safe.', true);
   }catch(e){ show('Encryption failed in this browser.', false); }
 }
 async function decrypt(){
   if(!input.value){ show('Please paste an encrypted code first.', false); return; }
   if(!pass.value){ show('Please type the password.', false); return; }
   try{
     var data=unb64(input.value);
     var salt=data.slice(0,16), iv=data.slice(16,28), ct=data.slice(28);
     var key=await getKey(pass.value, salt);
     var pt=await crypto.subtle.decrypt({name:'AES-GCM', iv:iv}, key, ct);
     output.value=new TextDecoder().decode(pt); show('Decrypted successfully.', true);
   }catch(e){ show('Decryption failed — wrong password or the code is damaged / incomplete.', false); }
 }
 document.getElementById('encBtn').addEventListener('click', encrypt);
 document.getElementById('decBtn').addEventListener('click', decrypt);
 document.getElementById('copyBtn').addEventListener('click', function(){ if(output.value&&navigator.clipboard) navigator.clipboard.writeText(output.value); });
})();
</script>
@endsection
