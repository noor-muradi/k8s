<h1> How to install Velero and create backup of K8s resources</h1>

1. Download velero latest binary from https://github.com/vmware-tanzu/velero/releases

2. Create s3 bucket:
   
```
aws s3 mb s3://<bucket-name> --region <region>
```

3. Create IAM user and required permission:
   
```
aws iam create-user --user-name velero
```

Create IAM policy and attach policy to give velero the necessary permissions:

```
cat > velero-policy.json <<EOF
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "ec2:DescribeVolumes",
                "ec2:DescribeSnapshots",
                "ec2:CreateTags",
                "ec2:CreateVolume",
                "ec2:CreateSnapshot",
                "ec2:DeleteSnapshot"
            ],
            "Resource": "*"
        },
        {
            "Effect": "Allow",
            "Action": [
                "s3:GetObject",
                "s3:DeleteObject",
                "s3:PutObject",
                "s3:AbortMultipartUpload",
                "s3:ListMultipartUploadParts"
            ],
            "Resource": [
                "arn:aws:s3:::${BUCKET}/*"
            ]
        },
        {
            "Effect": "Allow",
            "Action": [
                "s3:ListBucket"
            ],
            "Resource": [
                "arn:aws:s3:::${BUCKET}"
            ]
        }
    ]
}
EOF

```
Attach the policy to the velero IAM user:

```
aws iam put-user-policy \
  --user-name velero \
  --policy-name velero \
  --policy-document file://velero-policy.json
```  

Create an access key for the velero user:
    
```
aws iam create-access-key --user-name velero
```

4. Create a Velero-specific credentials file (credentials-velero) in your local directory:

```
    [default]
    aws_access_key_id=<AWS_ACCESS_KEY_ID>
    aws_secret_access_key=<AWS_SECRET_ACCESS_KEY>
 ```
5. Install and start Velero

```
velero install \
    --provider aws \
    --bucket <bucket-name> \
    --secret-file ./credentials-velero \
    --backup-location-config region=<region> \
    --snapshot-location-config region=<region> \
    --plugins velero/velero-plugin-for-aws:v1.8.0
```    

6. backup your resources in a particular namespace:
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
7. restore your resources:
```
velero restore create --from-backup my-app-backup
```
8. schedule backup:
```
velero create schedule <schedule-name> --schedule="0 0 * * *" --include-namespaces=<namespace1,namespace2> --ttl 168h
```
