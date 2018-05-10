
# SSH and User permissions

SSH doesn’t like it if your home or ~/.ssh directories have group write permissions. Your home directory should be writable only by you, ~/.ssh should be 700, and authorized_keys should be 600:
```
chmod g-w /home/your_user
chmod 700 /home/your_user/.ssh
chmod 600 /home/your_user/.ssh/authorized_keys
```

Taken from https://www.daveperrett.com/articles/2010/09/14/ssh-authentication-refused/
