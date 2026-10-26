p="app/Providers/FortifyServiceProvider.php"
s=open(p).read()
if "use Illuminate\\Support\\Facades\\Hash;" not in s:
    s=s.replace("use Illuminate\Support\Facades\RateLimiter;", "use Illuminate\Support\Facades\Hash;\nuse Illuminate\Support\Facades\RateLimiter;")
if "use App\Models\User;" not in s:
    s=s.replace("use App\Actions\Fortify\CreateNewUser;", "use App\Actions\Fortify\CreateNewUser;\nuse App\Models\User;")
# insert authenticateUsing in configureActions
s=s.replace("        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);", "        Fortify::createUsersUsing(CreateNewUser::class);\n        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);\n        Fortify::authenticateUsing(function ($request) {\n            $user = User::where('email', $request->email)->first();\n            if (! $user || ! Hash::check($request->password, $user->password)) {\n                return null;\n            }\n            if (! $user->isActive()) {\n                return null;\n            }\n            return $user;\n        });")
# remove duplicate createUsers line? configureActions originally only reset; our replace added createUsers; check
open(p,"w").write(s)
print("done")
