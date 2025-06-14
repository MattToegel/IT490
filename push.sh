
#source = $1
#user = $2
#host = $3
#dest = $4
scp -i ~/.ssh/id_rsa_migration -r $1 $2@$3:$4
