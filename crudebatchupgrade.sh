source=$1
destination=$2

for filename in $source/*.php; do
        base=$(basename "$filename")
        echo $(basename "$filename")
        cp $destination/$base ./backup/$base.$(date "+%Y%m%d_+%H-%M")
        echo backed up $base
        #TODO verify file backed up
        rm -f $destination/$base
        echo deleted $base
        cp $filename $destination
        echo copied $filename to "./live/$base"

done
