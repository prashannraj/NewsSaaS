p="app/Providers/FortifyServiceProvider.php"
s=open(p).read()
s=s.replace("use App\Actions\Fortify\ResetUserPassword;", "use App\Actions\Fortify\CreateNewUser;\nuse App\Actions\Fortify\ResetUserPassword;")
s=s.replace("        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);", "        Fortify::createUsersUsing(CreateNewUser::class);\n        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);")
open(p,"w").write(s)
print("done")
