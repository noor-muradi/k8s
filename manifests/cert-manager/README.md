**Install cert managaer on k8s:**

install cert manager on kubernetes cluster using helm chart.

1. add & update cert-manager helm repo

```
helm repo add jetstack https://charts.jetstack.io

helm repo update
```

2. install cert manager

```
helm install \
cert-manager jetstack/cert-manager \
--namespace cert-manager \
--create-namespace \
--version v1.11.0 \
--set installCRDs=true
```
4.deploy [cert issuer](https://github.com/noor-muradi/k8s/blob/3320961fb3341bf279bd830938dde222d4f0c1ec/manifests/cert-manager/cert-issuer.yaml) on your k8s cluster.
```
kubectl apply -f cert-issuer.yaml 
```

