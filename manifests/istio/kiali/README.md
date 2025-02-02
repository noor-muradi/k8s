<h1>Deploy Kiali using helm chart</h1>

1. Add kiali Helm repo:
```
helm repo add kiali https://kiali.org/helm-charts
helm repo update
```
2. Deploy the Kiali server using custom values:

```
helm install kiali kiali/kiali-server -n istio-system -f values.yaml
```
