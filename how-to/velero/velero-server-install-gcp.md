<h1> How to install Velero and create backup of K8s resources</h1>

*1.create GCP backet*
```
gsutil mb gs://backup
```
*2.create GCP service account:*
```
GSA_NAME=velero
gcloud iam service-accounts create $GSA_NAME \
    --display-name "Velero service account" 
```
*3.Create Custom Role with Permissions for the Velero GSA:*

Create environment variable for custom role:
```
ROLE_PERMISSIONS=(
    compute.disks.get
    compute.disks.create
    compute.disks.createSnapshot
    compute.snapshots.get
    compute.snapshots.create
    compute.snapshots.useReadOnly
    compute.snapshots.delete
    compute.zones.get
    storage.objects.create
    storage.objects.delete
    storage.objects.get
    storage.objects.list
    iam.serviceAccounts.signBlob
)

```
*create the custom role*
```
gcloud iam roles create velero.server \
    --project $PROJECT_ID \
    --title "Velero Server" \
    --permissions "$(IFS=","; echo "${ROLE_PERMISSIONS[*]}")"
```
*Bind the role to service account*
```
gcloud projects add-iam-policy-binding $PROJECT_ID \
    --member serviceAccount:$SERVICE_ACCOUNT_EMAIL \
    --role projects/$PROJECT_ID/roles/velero.server
```
*Give "object admin" role to service account for the bucket*
```
gsutil iam ch serviceAccount:$SERVICE_ACCOUNT_EMAIL:objectAdmin gs://${BUCKET}
```


*4.Create a service account key, specifying an output file (credentials-velero).*
```
gcloud iam service-accounts keys create credentials-velero \
    --iam-account $SERVICE_ACCOUNT_EMAIL
```


*5. download the Velero cli and put it in /usr/local/bin:*
```
wget https://github.com/vmware-tanzu/velero/releases/download/v1.11.0/velero-v1.11.0-linux-amd64.tar.gz

tar -xvzf velero-v1.10.3-linux-amd64.tar.gz && mv velero-v1.10.3-linux-amd64/velero /usr/local/bin/
```
*6. install velero server:*
```
velero install \ 
--provider gcp \ 
--plugins velero/velero-plugin-for-gcp \
--bucket $BUCKET \
--secret-file ./credentials-velero
```
*7. backup your resources in a particular namespace:*
```
velero backup create my-app-bakcup --include-namespaces my-app
```
list your backup using command:  
```
velero backup get my-app-backup
```
get detailed information of your backup:
```
velero backup describe my-app-backup
```
*8. restore your resources:*
```
velero restore create --from-backup my-app-backup
```
*9. schedule backup:*
```
velero create schedule <schedule-name> --schedule="0 0 * * *" --include-namespaces=<namespace1,namespace2> --ttl 168h
```
