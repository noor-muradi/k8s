<h1>K8s cluster install on Ubuntu 20.04.</h1>


Install Kubernetes on Master Node

Upgrade apt packages
```
sudo apt update
sudo apt upgrade
```

Create configuration file for containerd:
```
echo "overlay br_netfilter" > /etc/modules-load.d/containerd.conf
```
Load modules:
```
sudo modprobe overlay
sudo modprobe br_netfilter
```

Set system configurations for Kubernetes networking:
```
cat <<EOF | sudo tee /etc/sysctl.d/99-kubernetes-cri.conf
net.bridge.bridge-nf-call-iptables = 1
net.ipv4.ip_forward = 1
net.bridge.bridge-nf-call-ip6tables = 1
EOF
```

Apply new settings:
```
sudo sysctl --system
```
*Install containerd:*
```
sudo apt-get update && sudo apt-get install -y containerd
```

Create default configuration file for containerd:
```
sudo mkdir -p /etc/containerd
```

Generate default containerd configuration and save to the newly created default file:
```
sudo containerd config default | sudo tee /etc/containerd/config.toml
```

Restart containerd to ensure new configuration file usage:
```
sudo systemctl restart containerd
```

Verify that containerd is running.
```
sudo systemctl status containerd
```

Disable swap:
```
sudo swapoff -a
```
make sure to disable swap permanently by commenting it in /etc/fstab file.


Install dependency packages:
```
sudo apt-get update && sudo apt-get install -y apt-transport-https curl
```

Download and add GPG key:
```
curl -s https://packages.cloud.google.com/apt/doc/apt-key.gpg | sudo apt-key add -
```

Add Kubernetes to repository list:
```
cat <<EOF | sudo tee /etc/apt/sources.list.d/kubernetes.list
deb https://apt.kubernetes.io/ kubernetes-xenial main
EOF
```

Update package listings:
```
sudo apt-get update
```

Install Kubernetes packages (Note: If you get a dpkg lock message, just wait a minute or two before trying the command again):
```
sudo apt install -y kubelet=1.21.0-00 kubeadm=1.21.0-00 kubectl=1.21.0-00
```

Turn off automatic updates:
```
sudo apt-mark hold kubelet kubeadm kubectl
```

*Log into both Worker Nodes to perform previous steps 1 to 18.*


Initialize the Kubernetes cluster on the control plane node using kubeadm (Note: This is only performed on the Control Plane Node):
```
sudo kubeadm init --pod-network-cidr 192.168.0.0/16 --kubernetes-version 1.21.0
```

Set kubectl access:
```
mkdir -p $HOME/.kube
sudo cp -i /etc/kubernetes/admin.conf $HOME/.kube/config
sudo chown $(id -u):$(id -g) $HOME/.kube/config
```

Test access to cluster:
```
kubectl get nodes
```

Install the Calico Network Add-On:

On the Control Plane Node, install Calico Networking;
```
kubectl apply -f https://raw.githubusercontent.com/projectcalico/calico/master/manifests/calico.yaml
```

Wait for 2-4 Min and Check status of the control plane node:
```
kubectl get nodes
```

Join the Worker Nodes to the Cluster

In the Control Plane Node, create the token and copy the kubeadm join command (NOTE:The join command can also be found in the output from kubeadm init command):
```
kubeadm token create --print-join-command
```
In both Worker Nodes, paste the kubeadm join command to join the cluster. Use sudo to run it as root:
```
sudo kubeadm join ...
```
In the Control Plane Node, view cluster status (Note: You may have to wait a few moments to allow all nodes to become ready):
```
kubectl get nodes
```

Additional Steps:

Open below ports on nodes before enabling the firewall:
```
179,80,443,2022,2379,2380,2381,6443,10249,10250,10256,10257,10259
```
Set the containerd unix socket address:
```
echo "runtime-endpoint: unix:///run/containerd/containerd.sock" > /etc/crictl.yaml
```
