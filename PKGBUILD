# Maintainer: Jefferson Gonzalez <jgmdev@gmail.com>

pkgname=infocus
pkgver=0.1
pkgrel=1
pkgdesc="Automatic activity time tracker application."
arch=('any')
url="http://github.com/infocus/infocus"
license=('GPL')
depends=('php' 'php-sqlite' 'wmctrl' 'xorg-xprop' 'chromium' 'xprintidle')
makedepends=('composer')
install="${pkgname}.install"
#source=( "future sources" )
#md5sums=( 'SKIP' )

prepare() {
  cd "${srcdir}/../"
  composer install
}

package() {
  cd "${srcdir}/../"
  DESTDIR="$pkgdir" ./install.sh
}
