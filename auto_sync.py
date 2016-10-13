import pexpect
import sys

git_user = 'jkzleond'
git_pwd = 'jk19831123'
ssh_pwd = 'palmlink)*&!kms'
target_dir = '/disk2/var/www/html/php/car_mate_admin'

child = pexpect.spawn('git add .', echo=True)
# child.expect('$')
# child.sendline('git commit -m "commit"')
child.expect('$')
child.sendline('git push')
# child.expect(':')
# child.sendline(git_usr)
# child.expect('Password')
# child.sendline(git_pwd)
# child.expect('$')
# child.sendline('ssh root@116.55.248.76')
# child.expect('password')
# child.sendline(ssh_pwd)
# child.expect('#')
# child.sendline('ssh -p 6008 root@116.55.248.76')
# child.expect('password')
# child.sendline(ssh_pwd)
# child.expect('#')
# child.sendline('cd ' + target_dir)
# child.expect('#')
# child.sendline('git pull')
# child.expect('Username')
# child.sendline(git_usr)
# child.expect('Password')
# child.sendline(git_pwd)
# child.expect('#')
# child.sendline('exit')
# child.expect('#')
# child.sendline('exit')
# child.close()


