p="config/fortify.php"
s=open(p).read()
s=s.replace("        Features::resetPasswords(),\n", "        Features::registration(),\n        Features::resetPasswords(),\n        Features::emailVerification(),\n")
open(p,"w").write(s)
print("done")
