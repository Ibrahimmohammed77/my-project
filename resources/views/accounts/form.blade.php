<x-form.input name="full_name" label="Full Name" :value="$account->full_name ?? ''" required />
<x-form.input name="username" label="Username" :value="$account->username ?? ''" required />
<x-form.input name="email" label="Email" type="email" :value="$account->email ?? ''" required />
<x-form.input name="phone" label="Phone" :value="$account->phone ?? ''" />

<x-form.select 
    name="account_status_id" 
    label="Status" 
    :options="$statuses" 
    :selected="$account->account_status_id ?? ''" 
    required 
/>

<x-form.input name="password" label="Password" type="password" :required="!isset($account)" />
<small class="text-muted d-block mb-3">Leave password blank to keep current password (only for edit).</small>
