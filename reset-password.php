$account = App\Domain\Identity\Models\Account::first();
if($account) {
    $account->password_hash = bcrypt('password');
    $account->save();
    echo 'Password updated for: ' . $account->username . PHP_EOL;
    echo 'Username: ' . $account->username . PHP_EOL;
    echo 'Password: password' . PHP_EOL;
} else {
    echo 'No account found' . PHP_EOL;
}
