p="app/Providers/FortifyServiceProvider.php"
s=open(p).read()
s=s.replace("        Fortify::loginView(fn () => view('pages::auth.login'));\n", "        Fortify::loginView(fn () => view('pages::auth.login'));\n        Fortify::registerView(fn () => view('pages::auth.register'));\n        Fortify::verifyEmailView(fn () => view('pages::auth.verify-email'));\n        Fortify::confirmPasswordView(fn () => view('pages::auth.confirm-password'));\n")
open(p,"w").write(s)
print("done")
