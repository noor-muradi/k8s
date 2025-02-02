<h1> How to deploy NFS Subdir External Provisioner to your cluster</h1>

1. setup NFS server and make sure the cluster nodes have access to NFS share.

2. Deploy "NFS Subdir External Provisioner" to your cluster:

**Using Helm**
```
$ helm repo add nfs-provisioner https://kubernetes-sigs.github.io/nfs-subdir-external-provisioner/
$ helm install nfs nfs-provisioner/nfs-subdir-external-provisioner \
    --set nfs.server=x.x.x.x \
    --set nfs.path=/exported/path
```
3. create a storage class:
```
apiVersion: storage.k8s.io/v1
kind: StorageClass
metadata:
  name: nfs-default
provisioner: cluster.local/nfs-subdir-external-provisioner
reclaimPolicy: Retain
allowVolumeExpansion: true
volumeBindingMode: Immediate
mountOptions:
  - vers=4  # NFS version (modify as needed)
parameters:
  server: 192.168.30.9  
  path: /nfs/default/  

```

4. create Persistent Volume Claim.

```
apiVersion: v1
kind: PersistentVolumeClaim
metadata:
  name: nfs-pvc
  namespace: example
spec:
  accessModes:
    - ReadWriteOnce
  resources:
    requests:
      storage: 2Gi
  storageClassName: nfs-client  #default class name or created custom storage class: nfs-default
```

5. define PVC in the pod manifest:

```
apiVersion: v1
kind: Pod
metadata:
  labels:
    run: ubuntu
  name: ubuntu
  namespace: example
spec:
  containers:
  - image: ubuntu
    name: ubuntu
    command: ["sh","-c","while true;do tail -f /etc/passwd; done"]
    volumeMounts:
     - name: nfs-pv
       mountPath: /nfs
    resources: {}
  volumes:
    - name: nfs-pv
      persistentVolumeClaim:
        claimName: nfs-pvc
  restartPolicy: Always
```


**Install Multiple Provisioners**

It is possible to install more than one provisioner in your cluster to have access to multiple nfs servers and/or multiple exports from a single nfs server. Each provisioner must have a different storageClass.provisionerName and a different storageClass.name. For example:
```
helm install second-nfs nfs-provisioner/nfs-subdir-external-provisioner \
    --set nfs.server=y.y.y.y \
    --set nfs.path=/other/exported/path \
    --set storageClass.name=second-nfs-sc \
    --set storageClass.provisionerName=k8s-sigs.io/second-nfs
```

