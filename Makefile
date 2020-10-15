TAG = `git tag -l --contains origin/master`
PKG = itonomy_module-flowbox-$(TAG)
ZIP = $(PKG).zip

$(PKG):
	git checkout $(TAG)
	git pull
	rm -rf ./pkg $(PKG)
	mkdir -p $(PKG)
	cp composer.json LICENSE.md README.md $(PKG)/
	cp -R ./src/* $(PKG)/
	cp ./composer.json.marketplace $(PKG)/composer.json
	zip -qr $(ZIP) $(PKG)
	rm -rf $(PKG)/*
	mv $(ZIP) $(PKG)/
	mv $(PKG) ./pkg

.PHONY: clean

clean:
	rm -rf ./pkg
