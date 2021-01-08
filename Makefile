TAG := $(shell git tag -l --contains origin/master)
PKG := itonomy_module-flowbox-$(TAG)
ZIP := $(PKG).zip

module:
	git checkout $(TAG)
	rm -rf ./pkg $(PKG)
	mkdir -p $(PKG)
	cp LICENSE.md README.md $(PKG)/
	cp -R ./src/* $(PKG)/
	sed 's/src\///g' composer.json > $(PKG)/composer.json
	zip -qr $(ZIP) $(PKG)
	rm -rf $(PKG)/*
	mv $(ZIP) $(PKG)/
	mv $(PKG) ./pkg

.PHONY: clean

clean:
	rm -rf ./pkg
