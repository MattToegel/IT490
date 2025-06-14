user=$1
host=$2
loc=$3
#untested
scp $user@$host:$loc/* ~/preprocess

exec batchupgrade.sh preprocess live
