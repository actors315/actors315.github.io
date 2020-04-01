---  
layout: post  
type: blog  
title: 'Centos iptables-config 配置参数说明'  
date: 2019-06-13T10:14:12+08:00  
excerpt: '下面是Centos7 默认的iptables-config 配置
# Load additional iptables modules (nat helpers)
#   Default: -none'  
key: 11916b8987d62a4a5ecff523f7677bd1  
---  

下面是Centos7 默认的iptables-config 配置

```
# Load additional iptables modules (nat helpers)
#   Default: -none-
# Space separated list of nat helpers (e.g. 'ip_nat_ftp ip_nat_irc'), which
# are loaded after the firewall rules are applied. Options for the helpers are
# stored in /etc/modprobe.conf.
# 每次防火墙规则应用以后加载的模块,可以指定多个，以空格分开
IPTABLES_MODULES=""

# Unload modules on restart and stop
#   Value: yes|no,  default: yes
# This option has to be 'yes' to get to a sane state for a firewall
# restart or stop. Only set to 'no' if there are problems unloading netfilter
# modules.
# 在重新启动和停止iptables模块时，是否卸载此模块
IPTABLES_MODULES_UNLOAD="yes"

# Save current firewall rules on stop.
#   Value: yes|no,  default: no
# Saves all firewall rules to /etc/sysconfig/iptables if firewall gets stopped
# (e.g. on system shutdown).
# 当防火墙停止时，是否保存当前防火墙规则到iptables文件，默认不保存
IPTABLES_SAVE_ON_STOP="no"

# Save current firewall rules on restart.
#   Value: yes|no,  default: no
# Saves all firewall rules to /etc/sysconfig/iptables if firewall gets
# restarted.
# 当防火墙重启时，是否保存当前防火墙规则到iptables文件，默认不保存
IPTABLES_SAVE_ON_RESTART="no"

# Save (and restore) rule and chain counter.
#   Value: yes|no,  default: no
# Save counters for rules and chains to /etc/sysconfig/iptables if
# 'service iptables save' is called or on stop or restart if SAVE_ON_STOP or
# SAVE_ON_RESTART is enabled.
# 重启时是否保存并恢复所有chain和规则中的数据包和字节计数器，默认否
IPTABLES_SAVE_COUNTER="no"

# Numeric status output
#   Value: yes|no,  default: yes
# Print IP addresses and port numbers in numeric format in the status output.
# 输出的IP地址是数字的格式，而不是域名和主机名的形式，yes：（默认值）在状态输出中只包括IP地址，no：在状态输出中返回域名或主机名
IPTABLES_STATUS_NUMERIC="yes"

# Verbose status output
#   Value: yes|no,  default: yes
# Print info about the number of packets and bytes plus the "input-" and
# "outputdevice" in the status output.
# 输出iptables状态时，是否包含输入输出设备，默认是
IPTABLES_STATUS_VERBOSE="no"

# Status output with numbered lines
#   Value: yes|no,  default: yes
# Print a counter/number for every rule in the status output.
# 输出iptables状态时，是否同时输出每条规则的匹配数，默认是
IPTABLES_STATUS_LINENUMBERS="yes"

# Reload sysctl settings on start and restart
#   Default: -none-
# Space separated list of sysctl items which are to be reloaded on start.
# List items will be matched by fgrep.
# 在启动和重新启动时重新加载sysctl设置，指定配置文件
#IPTABLES_SYSCTL_LOAD_LIST=".nf_conntrack .bridge-nf"

# Set wait option for iptables-restore calls in seconds
#   Default: 600
# Set to 0 to deactivate the wait.
# 装载由iptables-save保存的规则集的等待时间，默认600秒，设置为0表示不等待
#IPTABLES_RESTORE_WAIT=600

# Set wait interval option for iptables-restore calls in microseconds
#   Default: 1000000
# Set to 100000 to try to get the lock every 100000 microseconds, 10 times a
# second.
# Only usable with IPTABLES_RESTORE_WAIT > 0
# 和上面一样，但单位是微秒
#IPTABLES_RESTORE_WAIT_INTERVAL=1000000
```