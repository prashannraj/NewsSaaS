p="resources/views/pages/auth/login.blade.php"
s=open(p).read()
needle="        </form>\n\n    </div>"
addition="""        </form>

        <div class="flex flex-col gap-3">
            @foreach (config('oauth.providers', ['google']) as $provider)
                <flux:button :href="route('social.redirect', $provider)" variant="outline" class="w-full">
                    {{ __('Continue with :provider', ['provider' => ucfirst($provider)]) }}
                </flux:button>
            @endforeach
        </div>

    </div>"""
s=s.replace(needle, addition, 1)
open(p,"w").write(s)
print("done")
