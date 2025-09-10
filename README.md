# BCMath Utils

Extends the BCMath function set with support for converting scientific notation to standard decimal format.  
（扩展了 BCMath 函数集，提供将科学计数法转换为标准十进制格式的支持。）

## Installation

```bash
composer require beacoria/bc
```
## Usage / 使用示例

```php
<?php
require 'src/helpers.php';

// -------------------------
// 1️⃣ 科学计数法转换
// -------------------------
echo normalizeNumberForBc('1e3');        // 1000
echo normalizeNumberForBc('1E3');        // 1000
echo normalizeNumberForBc('1.23e4');     // 12300
echo normalizeNumberForBc('1.23E4');     // 12300
echo normalizeNumberForBc('-1e3');       // -1000
echo normalizeNumberForBc('1e-3');       // 0.001
echo normalizeNumberForBc('-1.23e-4');   // -0.000123
echo normalizeNumberForBc('3.45E+6');    // 3450000
echo normalizeNumberForBc('1230.123000');    // 1230.123
echo normalizeNumberForBc(' 1230.123000 ');    // 1230.123
echo normalizeNumberForBc(' 0.000 ');    // 0

// -------------------------
// 2️⃣ 小数位处理（科学记数法也支持）
// -------------------------
echo decimalPlaces('1.23e-4', '4.567e2', '0.1');
// 输出: 6 -> 返回最大的小数位数
echo decimalPlaces('0.000');
// 输出: 0

// -------------------------
// 3️⃣ 大小比较（科学记数法数字）
// -------------------------
var_dump(gt('1.23e3', '1.2e3'));      // true
var_dump(gt0('-0.1e2'));              // false
var_dump(lt('1.234e-3', '1.234e-3', false)); // false  不包含等于

// -------------------------
// 4️⃣ 多个数字运算（支持科学记数法）
// -------------------------
echo bcaddd('1.23e3', '4.567e2', '0.1');       // 1686.8
echo bcsubSafe('5.5e1', '25');              // 30
echo bcmulSafe('1.2e2', '3.4e1');              // 4080
echo bcdivSafe('1.0e4', '4e1');                // 250

// -------------------------
// 5️⃣ 清理小数位（科学记数法也支持）
// -------------------------
echo clearDecimal('1.2300e3');                 // 1230
echo clearDecimal('1.23456e-2', 4);           // 0.0123 -> 保留 4 位小数
```