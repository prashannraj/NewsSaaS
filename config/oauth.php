<?php

return [
    "providers" => array_filter(array_map("trim", explode(",", (string) env("OAUTH_PROVIDERS", "google")))),
    "allow_registration" => (bool) env("OAUTH_ALLOW_REGISTRATION", true),
];
