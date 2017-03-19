################################################################################
#
# linuxptp
#
################################################################################

LINUXPTP_VERSION = 8deb52
LINUXPTP_SITE = git://git.code.sf.net/p/linuxptp/code
CROSS_COMPILE = arm-linux-

define LINUXPTP_BUILD_CMDS
	$(MAKE) $(TARGET_CONFIGURE_OPTS) -C $(@D) all
endef

define LINUXPTP_INSTALL_STAGING_CMDS

endef

define LINUXPTP_INSTALL_TARGET_CMDS
	$(INSTALL) -D -m 0755 $(@D)/ptp4l  $(TARGET_DIR)/sbin/ptp4l
	$(INSTALL) -D -m 0755 $(@D)/pmc $(TARGET_DIR)/sbin/pmc
endef

$(eval $(generic-package))

