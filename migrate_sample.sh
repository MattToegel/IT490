#we can maybe hardcode static ref to host
host=""
user=""
#get source from arg or default to current dir
[ -z "$1" ] && source=. || source=$1
if test -z "$2"
then
 echo "Must pass a destination"
 exit
fi
dest=$2
echo "Pushing data"
sudo ./push.sh $source $user $host $dest
echo"Running remote migrate"
ssh -i ~/.ssh/id_rsa_migration $user@$host /home/ubuntu/do_migrate.sh
