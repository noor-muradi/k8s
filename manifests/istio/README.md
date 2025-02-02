Install Istio on k8s:

1. Download "istioctl" binary:
   
   a. Go to https://github.com/istio/istio/releases and download latest version:
   
   b . extract the downloaded zip and move the istioctl to /usr/local/bin and set the env variable.
   
2. Install istio:
   
```
istioctl install --set profile production
```

3. Add a namespace label to instruct Istio to automatically inject Envoy sidecar proxies when you deploy your application:
```
kubectl label namespace default istio-injection=enabled
```

4. Deploy your microservices application:

```
kubectl apply -f istio-1.20.0/samples/bookinfo/platform/kube/bookinfo.yaml
```
