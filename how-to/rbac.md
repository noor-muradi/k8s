# Add User in Kubernetes Cluster
1. Create Name Space
```
kubectl create namespace development
```
2. Create private key and a CSR (Certificate Signing Request) for DevUser
```   
sudo openssl genrsa -out DevUser.key 2048
sudo openssl req -new -key DevUser.key -out DevUser.csr -subj "/CN=DevUser/O=development"
```
The common name (CN) of the subject will be used as username for authentication request. The organization field (O) will be used to indicate group membership of the user.

3. Provide CA keys of Kubernetes cluster to generate the certificate
```   
sudo openssl x509 -req -in DevUser.csr -CA path/to/k8s/ca.crt -CAkey path/to/ca.key -CAcreateserial -out DevUser.crt -days 45
```
or we can create a certificate signing request object:
```
apiVersion: certificates.k8s.io/v1
kind: CertificateSigningRequest
metadata:
  name: DevUser
spec:
  request: <base64 encoded csr>
  signerName: kubernetes.io/kube-apiserver-client
  expirationSeconds: 86400  # one day
  usages:
  - client auth
```
Create the DevUser csr object and approve it.
```
kubectl create -f DevUserCsr.yaml
kubectl certificate approve DevUser
```
Once approved, we can extract the certificate
```
kubectl get csr DevUser -o jsonpath='{.status.certificate}'| base64 -d > DevUser.crt
```
**on the client system**

4. copy DevUser.crt, DevUser.key and /etc/kubernetes/pki/ca.crt to client machine
5. Get Kubernetes Cluster Config
```
kubectl config view
```
6. Add the user in the kubeconfig file.
```
kubectl config set-cluster my-cluster-name --server=https://<cluster-url>:<port> --certificate-authority=<path-to-ca-certificate-file>
kubectl config set-credentials DevUser --client-certificate=<path/to//DevUser.crt> --client-key=</path/to/DevUser.key>
```
7. Get Kubernetes Cluster Config
```   
kubectl config view
```
9. Add a context in the config file, that will allow this user (DevUser) to access the development namespace in the cluster.
```
kubectl config set-context DevUser-context --cluster=my-cluster-name --namespace=development --user=DevUser
kubectl config use-context DevUser-context
kubectl config current-context
```

**On K8s Cluster**

10. Create a Role for the DevUser :
```
kubectl create role pod-reader-role -n development --verb get,list,watch --resources pod, pod/status
```
11. Verify Role
```
kubectl get role -n development
```
12. Bind the Role to the dev User and Verify Your Setup Works
Create the RoleBinding spec file
```
kubectl create rolebinding $rolebinding-name --role $role-name --user DevUser  -n development 
```
13. Test access by attempting to list pods.
```
kubectl get pods --context=DevUser-context
```
14. Create Pod 
```
kubectl run nginx --image=nginx --context=DevUser-context
```
