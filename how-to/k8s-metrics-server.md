<h1>How to install metrics server on K8s</h1>

Step 1: Download Metrics Server Manifest
The first step is to download the latest Metrics Server manifest file from the Kubernetes GitHub repository. Use below curl command to download yaml file.
```
curl -LO https://github.com/kubernetes-sigs/metrics-server/releases/latest/download/components.yaml
```
If you are planning to install metrics server in high availability mode then download following manifest file.
```
curl https://github.com/kubernetes-sigs/metrics-server/releases/latest/download/high-availability-1.21+.yaml
```
Step 2: Modify Metrics Server Yaml File
```
 vi components.yaml
```
Find the args section under the container section, add the following line:
```
- --kubelet-insecure-tls
```
Under the spec section add following parameter.
```
hostNetwork: true
```
![Edit-Metrics-Server jpg](https://github.com/noor-muradi/k8s/assets/24589033/a7228ec3-9c9b-4a32-84ab-171b63cace53)

Save and close the file.

Step 3: Deploy Metrics Server

```
kubectl apply -f components.yaml
```

Verify Metrics Server Deployment:

```
kubectl get pods -n kube-system
```
![Metrics-Server-Pods-Status](https://github.com/noor-muradi/k8s/assets/24589033/e0590595-a37c-4a3a-a7e0-5d75d000431b)

test the metrics:

```
 kubectl top pod -n kube-system
 ```
 

 
