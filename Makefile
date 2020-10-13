SRCDIR = ./src
CURRENT_BRANCH := `git rev-parse --abbrev-ref HEAD`
TAG := `git tag -l --contains origin/master`
PKGDIR := ./package/$(TAG)


package:
	git checkout origin/$(TAG)
	rm -rf $(PKGDIR)
	mkdir -p $(PKGDIR)
	cp composer.json LICENSE.md README.md $(PKGDIR)/
	cp -R $(SRCDIR)/* $(PKGDIR)/
	zip -r itonomy_module-flowbox-$(TAG).zip $(PKGDIR)/
	rm -rf $(PKGDIR)/*
	mv itonomy_module-flowbox-$(TAG).zip $(PKGDIR)/
	git checkout $(CURRENT_BRANCH)

.PHONY: clean

clean:
	rm -rf $(PKGDIR)
