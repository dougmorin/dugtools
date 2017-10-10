
# Htaccess snippets

## The following will block everybody except for a certain specific set of IP's from viewing the site.
```
order deny,allow
# allow partial ip addresses
allow from 192.168
allow from 219

# allow full ip addresses
allow from 127.0.0.1 #local
allow from localhost #local
allow from 000.000.000.000 # replace this with the IP to allow
deny from all
```

## This snippet will only block a certain IP address from viewing the site
```
order allow,deny
# partial ip addresses blocking
deny from 192.168
deny from 219

# full ip addresses blocking
deny from 000.000.000.000
allow from all
```
