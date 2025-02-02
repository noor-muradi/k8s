<h2> How to backup etcd in K8s</h2>

**1. Download etcd cli:**
```
curl -s https://api.github.com/repos/etcd-io/etcd/releases/latest | grep browser_download_url | grep linux-amd64 | cut -d '"' -f 4 | wget -qi


tar xzvf *.tar.gz
cd etcd-*/
mv etcd* /usr/local/bin/
```
**2. Find K8s manifests location**
```
cat /var/lib/kubelet/config.yam
```
*normally it is in /etc/kubernetes/manifests/*
```
cat /etc/kubernetes/manifests/etcd.yaml
```

*needed information from the output:*
```
spec:
  containers:
  - command:
    - etcd
    - --advertise-client-urls=https://192.168.20.10:2379
    - --cert-file=/etc/kubernetes/pki/etcd/server.crt
    - --client-cert-auth=true
    - --data-dir=/var/lib/etcd
    - --initial-advertise-peer-urls=https://192.168.20.10:2380
    - --initial-cluster=master=https://192.168.20.10:2380
    - --key-file=/etc/kubernetes/pki/etcd/server.key
    - --listen-client-urls=https://127.0.0.1:2379,https://192.168.20.10:2379
    - --listen-metrics-urls=http://127.0.0.1:2381
    - --listen-peer-urls=https://192.168.20.10:2380
    - --name=master
    - --peer-cert-file=/etc/kubernetes/pki/etcd/peer.crt
    - --peer-client-cert-auth=true
    - --peer-key-file=/etc/kubernetes/pki/etcd/peer.key
    - --peer-trusted-ca-file=/etc/kubernetes/pki/etcd/ca.crt
    - --snapshot-count=10000
    - --trusted-ca-file=/etc/kubernetes/pki/etcd/ca.crt
```
**3. take backup of etcd snapshot using below command:**
```
sudo ETCDCTL_API=3 etcdctl snapshot save etcd-snapshot.db --cacert /etc/kubernetes/pki/etcd/ca.crt --cert /etc/kubernetes/pki/etcd/server.crt --key /etc/kubernetes/pki/etcd/server.key
```
**4.Back up the certificates and key files:**
```
sudo tar -zcvf etcd.tgz /etc/kubernetes/pki/etcd
```
**5. Restore etcd using below command:**
```
ETCDCTL_API=3 etcdctl snapshot restore etcd-snapshot.db --endpoints=https://192.168.20.10:2379 --cacert=/etc/kubernetes/pki/etcd/ca.crt --cert=/etc/kubernetes/pki/etcd/server.crt --key=/etc/kubernetes/pki/etcd/server.key --name=master --data-dir=/var/lib/etcd --initial-cluster=master=https://192.168.20.10:2380 --initial-cluster-token=etcd-cluster --initial-advertise-peer-urls=https://192.168.20.10:2380

```
