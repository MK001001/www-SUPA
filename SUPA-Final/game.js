//Javascript Document
const width = 1000;
const height = 800;
var config = {
  type: Phaser.AUTO,
  width: width,
  height: height,
  physics: {
    default: 'arcade',
    arcade: {
      gravity: { y: 1400 },
    }
  },
  scene: {
    preload: preload,
    create: create,
    update: update
  }
};

let frames = (num) => { return (num * 1000) / 60; }
var game = new Phaser.Game(config);
let justDown = (key) => { return Phaser.Input.Keyboard.JustDown(key); }
let gameStart = false;
let loaded = false;
let p1wins = 0;
let p2wins = 0;
let roundStart = false;
let p1Char;
let p2Char;
let special;
let keys;
let wintxt = false;

function preload() {
  keys = {
    W: this.input.keyboard.addKey(Phaser.Input.Keyboard.KeyCodes.W),
    A: this.input.keyboard.addKey(Phaser.Input.Keyboard.KeyCodes.A),
    S: this.input.keyboard.addKey(Phaser.Input.Keyboard.KeyCodes.S),
    D: this.input.keyboard.addKey(Phaser.Input.Keyboard.KeyCodes.D),
    Z: this.input.keyboard.addKey(Phaser.Input.Keyboard.KeyCodes.Z),
    X: this.input.keyboard.addKey(Phaser.Input.Keyboard.KeyCodes.X),
    C: this.input.keyboard.addKey(Phaser.Input.Keyboard.KeyCodes.C),
    V: this.input.keyboard.addKey(Phaser.Input.Keyboard.KeyCodes.V),
    U: this.input.keyboard.addKey(Phaser.Input.Keyboard.KeyCodes.U),
    I: this.input.keyboard.addKey(Phaser.Input.Keyboard.KeyCodes.I),
    O: this.input.keyboard.addKey(Phaser.Input.Keyboard.KeyCodes.O),
    P: this.input.keyboard.addKey(Phaser.Input.Keyboard.KeyCodes.P),
  };
  let self = this;
  cursors = this.input.keyboard.createCursorKeys();

  this.load.image('dude', 'assets/phasericon.png');
  this.load.image('ken', 'assets/kenicon.png');
  this.load.image('health', 'assets/health.png');
  this.load.image('healthlost', 'assets/healthlost.png');
  this.load.image('sky', 'assets/sky.png');
  this.load.image('ground', 'assets/platform.png');
  this.load.image('star', 'assets/star.png');
  this.load.image('bomb', 'assets/bomb.png');
  this.load.spritesheet('dudesprite', 'assets/dude.png',
    { frameWidth: 32, frameHeight: 48 }
  );
  this.load.spritesheet('kensprite', 'assets/kensprite.png',
    { frameWidth: 70, frameHeight: 80 }
  );
  this.load.spritesheet('projectile', 'assets/projectile.png',
    { frameWidth: 10, frameHeight: 10 }
  );
}

var platforms;
var p1;
var p2;
var dash;
var airdash;
var numAirDash;
let player1;
let player2;
let jumpCount = 0;
let lastTime = 0;
let isAttacking = false;
let isAtkGrounded = false;

let kenicon;
let dudeicon;

class Player {
  constructor(player, enemy, char, health, speed, tr, add, physics, direction, controls, attackControls, timer) {
    this.isDashing = false
    this.player = player;
    this.enemy = enemy;
    this.char = char;
    this.maxHp = health;
    this.currentHp = this.maxHp;
    this.speed = speed;
    this.tr = tr;
    this.add = add;
    this.physics = physics;
    this.direction = direction;
    this.controls = controls;
    this.jumpCount = jumpCount;
    this.lastTime = lastTime;
    this.isAttacking = false;
    this.isAtkGrounded = isAtkGrounded;
    this.dash = dash;
    this.airdashing = false;
    this.numAirDash = numAirDash;
    this.isDashing = false;
    this.healthrect;
    this.enemyClass;
    this.attackControls = attackControls;
    this.attacks = [];
    this.chars = [];
    this.crouching = false;
    this.crouchBlock = false;
    this.winCount = 0;
    this.timer = timer;
    this.stun = false;
    this.combocount = 0;

    if (this.char == 'dude')
      this.player.setScale(3);
    else if (this.char == 'ken')
      this.player.setScale(2);
  }

  airDash(distance, dir) {
    const position = this.player.x;
    if (dir == "left") {
      this.player.setPosition(position - distance, this.player.y);
    }
    if (dir == "right") {
      this.player.setPosition(position + distance, this.player.y);
    }
    this.numAirDash += 1;
    this.airdashing = false;

    if (this.direction !== dir)
      this.dash = false;
  }
  movement() {
    let jumpHeight = -700;
    let maxJumps = 2;
    let maxAirDash = 1;
    let self = this;

    if (this.controls[2].isDown && this.player.body.blocked.down) {
      this.crouching = true;
      this.player.setVelocityX(0);
      if (this.direction == 'left') {
        this.player.anims.play(this.char + '_crouchLeft', true);
        if (this.controls[3].isDown) {
          this.crouchBlock = true;
        }
        else {
          this.crouchBlock = false;
        }
      }

      if (this.direction == 'right') {
        this.crouching = true;
        this.player.anims.play(this.char + '_crouchRight', true);
        if (this.controls[1].isDown) {
          this.crouchBlock = true;
        }
        else {
          this.crouchBlock = false;
        }
      }
    }

    else if (this.airdashing)
      this.player.setVelocity(0);

    else {
      this.crouching = false;
      this.crouchBlock = false;

      if (this.dash == true) {
        if (this.direction == 'left') {
          if (this.controls[1].isDown) {
            this.player.setVelocityX(-400);
            this.player.anims.play(this.char + '_left', true);
          }
          else if (this.controls[3].isUp) {
            this.dash = false;
          }

          if (this.dash && this.controls[3].isDown && this.player.body.blocked.down && !this.isAttacking && !this.airdashing) {
            this.player.setVelocity(0);
            if (this.dash == true) {
              this.dash = false;
              this.isDashing = true;
              this.tr.delayedCall(250, function() {
                self.airDash(100, "right");
                self.player.setVelocity(125);
                self.dash = false;
                self.isDashing = false;
              });
            }
          }
        }

        else if (this.direction == 'right') {
          if (this.dash && this.controls[1].isDown && this.player.body.blocked.down && !this.isAttacking && !this.airdashing) {
            this.player.setVelocityX(0);
            if (this.dash == true) {
              this.dash = false;
              this.player.setVelocityX(0);
              this.isDashing = true;
              this.tr.delayedCall(250, function() {
                self.airDash(100, "left")
                self.player.setVelocityX(-125);
                self.dash = false;
                self.isDashing = false;
              });
            }
          }

          if (this.controls[3].isDown) {
            this.player.setVelocityX(400);
            this.player.anims.play(this.char + '_right', true);
          }

          else if (this.controls[1].isUp) {
            this.dash = false;
          }

        }
      }

      else if (this.controls[1].isDown) {
        if (this.direction == 'left') {
          this.player.setVelocityX(-200);
          this.player.anims.play(this.char + '_left', true);
        }

        if (this.direction == 'right') {
          this.player.setVelocityX(-125);
          this.player.anims.play(this.char + '_right', true);
        }
      }
      else if (this.controls[3].isDown) {
        if (this.direction == 'left') {
          this.player.setVelocityX(125);
          this.player.anims.play(this.char + '_left', true);
        }

        if (this.direction == 'right') {
          this.player.setVelocityX(200);
          this.player.anims.play(this.char + '_right', true);
        }
      }

      else {
        this.player.setVelocityX(0);

        if (this.player.body.blocked.down) {
          if (this.player.x > this.enemy.x)
            this.direction = 'left';

          else if (this.player.x < this.enemy.x)
            this.direction = 'right';
        }
        if (this.direction == 'left')
          this.player.anims.play(this.char + '_turnLeft', true);

        else if (this.direction == 'right')
          this.player.anims.play(this.char + '_turnRight', true);
      }
    }



    if (this.player.body.blocked.down) {
      this.jumpCount = 0;
      if (!this.controls[1].isDown || !this.controls[3].isDown) {
        this.numAirDash = 0;
        this.airdashing = false;
      }
    }

    if (this.player.body.blocked.down == false) {
      if (this.numAirDash < maxAirDash && this.airdashing && !this.isAttacking) {
        if (this.controls[1].isDown) {
          this.player.body.setAllowGravity(false);
          this.player.setVelocityX(0);
          this.player.setVelocityY(0);
          this.isDashing = true;
          this.tr.delayedCall(500, function() {
            if (self.numAirDash < maxAirDash && self.airdashing) {
              self.airDash(150, "left");
              self.player.setVelocityX(-125);
              self.player.body.setAllowGravity(true);
              self.isDashing = false;
            }
          });
        }
        if (this.controls[3].isDown) {
          this.player.body.setAllowGravity(false);
          this.player.setVelocityX(0);
          this.player.setVelocityY(0);
          this.isDashing = true;
          this.tr.delayedCall(500, function() {
            if (self.numAirDash < maxAirDash && self.airdashing) {
              self.airDash(150, "right");
              self.player.setVelocityX(125);
              self.player.body.setAllowGravity(true);
              self.isDashing = false;
            }
          });
        }
      }
    }

    if (justDown(this.controls[0])) {
      if (this.player.body.blocked.down) {
        this.player.setVelocityY(jumpHeight);
        this.jumpCount = 1;
      }

      else if (this.jumpCount < maxJumps) {
        this.player.setVelocityY(jumpHeight);
        this.jumpCount += 1;
      }
    }
    if (this.controls[1].isUp && !this.isAttacking) {
      if (justDown(this.controls[3])) {
        let rclickDelay = this.tr.now - this.lastTime;
        this.lastTime = this.tr.now;
        if (rclickDelay < 250) {
          this.lastTime = 0;
          this.dash = true;
          if (this.player.body.blocked.down == false && this.numAirDash < maxAirDash) {
            this.airdashing = true;
          }
        }
      }
    }

    if (this.controls[3].isUp) {
      if (justDown(this.controls[1]) && !this.isAttacking) {
        let lclickDelay = this.tr.now - this.lastTime;
        this.lastTime = this.tr.now;
        if (lclickDelay < 250) {
          this.lastTime = 0;
          this.dash = true;
          if (this.player.body.blocked.down == false && this.numAirDash < maxAirDash) {
            this.airdashing = true;
          }
        }
      }
    }
  }
  attack = (person, where, character, atk, knockback, atkHeight, startLag, attackDuration, endLag, damage, maxHits, hboxwidth, hboxlen, offsetx, offsety, input) => {

    let self = this;
    let hits = 0;
    this.isAttacking = true;
    this.atkHeight = atkHeight;
    if (this.player.body.blocked.down == true) {
      this.isAtkGrounded = true;
      this.player.setVelocity(0);
    }
    this.player.anims.play(this.char + '_' + atk + this.direction);
    this.tr.delayedCall(frames(startLag), function() {
      self.atkHitbox = self.direction == 'right'
        ? self.add.rectangle((person.x + (person.width)) + offsetx, person.y + offsety, hboxwidth, hboxlen, 0xffffff, 0)
        : self.add.rectangle((person.x - (person.width)) - offsetx, person.y + offsety, hboxwidth, hboxlen, 0xffffff, 0);
      self.physics.add.existing(self.atkHitbox);
      self.atkHitbox.body.setAllowGravity(false);

      if (where === 'ground') {
        self.player.setVelocity(0);
        self.isAtkGrounded = true;
      }
      setInterval(function() {
        if (self.direction == 'right' && (self.atkHitbox.x !== person.x + (person.width * 2) || self.atkHitbox.y !== person.y)) {
          self.atkHitbox.x = (person.x + (person.width)) + offsetx;
          self.atkHitbox.y = person.y + offsety;
        }
        else if (self.direction == 'left' && (self.atkHitbox.x !== person.x - (person.width * 2) || self.atkHitbox.y !== person.y)) {
          self.atkHitbox.x = (person.x - (person.width)) - offsetx;
          self.atkHitbox.y = person.y + offsety;
        }
      }, 1);

      self.physics.add.overlap(self.atkHitbox, self.enemy, function() {
        if (hits < maxHits) {
          if (!self.enemyClass.block()) {
            self.damage(damage);
            hits += 1;
          }
        }
        if (self.direction == 'right')
          self.enemy.setVelocityX(knockback);
        else if (self.direction == 'left')
          self.enemy.setVelocityX(-knockback);
        for (let i = 0; i < self.attackControls.length; i++) {
          if (self.attackControls[i].isDown && self.attackControls[i] !== input && self.combocount <= 1) {
            console.log('cancel')
            self.combocount += 1;
            console.log(self.combocount)
            self.atkHitbox.destroy();
            self.isAttacking = false;
            self.isAtkGrounded = false;
          }
        }
      });
      self.tr.delayedCall(frames(attackDuration), function() {
        self.atkHitbox.destroy();
        self.tr.delayedCall(frames(endLag), function() {
          self.combocount = 0
          self.isAttacking = false;
          self.isAtkGrounded = false;
        });
      })
    });
  }


  projectile = (person, where, character, atk, knockback, atkHeight, startLag, attackDuration, endLag, damage, hboxwidth, hboxlen, speed) => {
    let self = this;
    this.atkHeight = atkHeight;
    this.isAttacking = true;

    if (this.player.body.blocked.down == true) {
      this.isAtkGrounded = true;
      this.player.setVelocity(0);
    }

    console.log(this.char + '_' + atk + this.direction)
    this.player.anims.play(this.char + '_' + atk + this.direction, true);
    this.tr.delayedCall(frames(startLag), function() {
      self.projecHitbox = self.direction == 'right'
        ? self.physics.add.sprite(person.x + (person.width * 2), person.y, this.player).setScale(2)
        : self.physics.add.sprite(person.x - (person.width * 2), person.y, this.player).setScale(2);

      self.projecHitbox.anims.play(self.char + '_' + atk + self.direction + '_atk');
      self.physics.add.existing(self.projecHitbox);
      self.projecHitbox.body.setAllowGravity(false);

      if (where === 'ground') {
        self.player.setVelocity(0);
        self.isAtkGrounded = true;
      }

      if (self.direction == 'right')
        self.projecHitbox.setVelocityX(speed);
      else if (self.direction == 'left')
        self.projecHitbox.setVelocityX(-speed);

      self.physics.add.overlap(self.projecHitbox, self.enemy, function() {
        if (!self.enemyClass.block())
          self.damage(damage);

        self.projecHitbox.destroy();
        if (self.direction == 'right')
          self.enemy.setVelocityX(knockback);
        else if (self.direction == 'left')
          self.enemy.setVelocityX(-knockback);
      });
      self.tr.delayedCall(frames(attackDuration), function() {
        if (self.projecHitbox)
          self.projecHitbox.destroy();
      })
      self.tr.delayedCall(frames(endLag), function() {
        self.isAttacking = false;
        self.isAtkGrounded = false;
      });
    });

  }

  healthbar = () => {
    let hpPercent = (this.currentHp / this.maxHp)
    if (this.healthrect == undefined) {
      if (this.player == p1) {
        this.add.rectangle((width) / 4, 50, (width * 5) / 14, 50, 0xf70400, 1)
        this.healthrect = this.add.rectangle((width) / 4, 50, (width * 5) / 14 * hpPercent, 50, 0xf7d600)
      }

      else if (this.player == p2) {
        this.add.rectangle((width) / 1.33, 50, (width * 5) / 14, 50, 0xf70400, 1)
        this.healthrect = this.add.rectangle((width) / 1.33, 50, (width * 5) / 14 * hpPercent, 50, 0xf7d600)
      }
    }

    if (hpPercent <= 0) {
      this.healthrect.width = 0;
    }

    else if (this.healthrect.width !== (width * 5) / 14 * hpPercent) {
      this.healthrect.width = (width * 5) / 14 * hpPercent
    }
  }

  winCounter = () => {
    this.square1;
    this.square2
    this.win1;
    this.win2;
    if (this.player == p1) {
      if (this.win1 == undefined)
        this.add.rectangle(width / 8, height / 8, (width) / 50, (width) / 50, 0xAEBBBF, 1);
      if (this.win2 == undefined)
        this.add.rectangle(width / 6, height / 8, (width) / 50, (width) / 50, 0xAEBBBF, 1);

      if (p1wins >= 1 && this.win1 == undefined) {
        this.win1 = this.add.rectangle(width / 8, height / 8, (width) / 49, (width) / 49, 0x4bbf70, 1);
      }
      if (p1wins >= 2 && this.win2 == undefined) {
        this.win2 = this.add.rectangle(width / 6, height / 8, (width) / 49, (width) / 49, 0x4bbf70, 1);
      }
    }

    else if (this.player == p2) {
      if (this.win1 == undefined)
        this.add.rectangle(width - (width / 8), height / 8, (width) / 50, (width) / 50, 0xAEBBBF, 1);
      if (this.win2 == undefined)
        this.add.rectangle(width - (width / 6), height / 8, (width) / 50, (width) / 50, 0xAEBBBF, 1);

      if (p2wins >= 1 && this.win1 == undefined) {
        this.win1 = this.add.rectangle(width - (width / 8), height / 8, (width) / 49, (width) / 49, 0x4bbf70, 1);
      }
      if (p2wins >= 2 && this.win2 == undefined) {
        this.win2 = this.add.rectangle(width - (width / 6), height / 8, (width) / 49, (width) / 49, 0x4bbf70, 1);
      }
    }
  }

  damage = (damage) => {
    let self = this;
    this.enemyClass.currentHp -= damage;
    this.enemyClass.stun = true;

    this.tr.delayedCall(damage * 25, function() {
      self.enemyClass.stun = false;
    });
  }

  attackController = (key, character) => {
    const self = this;
    let dude = ['dude',
      ['atk', this.player, 'ground', 'dude', 'punch', 150, 'low', 0, 10, 15, 10, 1, 30, 30, 0, 30, this.attackControls[0]],
      ['atk', this.player, 'ground', 'dude', 'head', 150, 'high', 0, 30, 30, 10, 1, 80, 80, -25, 0, this.attackControls[1]],
      ['atk', this.player, 'ground', 'dude', 'kick', 150, 'low', 0, 30, 20, 10, 1, 30, 30, 0, 50, this.attackControls[2]],
      ['projectile', this.player, 'ground', 'dude', 'projectile', 150, 'mid', 15, 15, 15, 5, 10, 30, 480]
    ];

    let ken = ['ken',
      ['atk', this.player, 'ground', 'ken', 'punch', 150, 'low', 10, 10, 30, 10, 1, 30, 100, 0, 0, this.attackControls[0]],
      ['atk', this.player, 'ground', 'ken', 'kick', 150, 'mid', 20, 15, 10, 10, 1, 30, 150, 0, 0, this.attackControls[1]],
      ['atk', this.player, 'ground', 'ken', 'revkick', 150, 'high', 5, 30, 10, 10, 1, 70, 180, 0, 0, this.attackControls[2]],
      ['projectile', this.player, 'ground', 'ken', 'hadoken', 150, 'mid', 30, 30, 30, 30, 15, 30, 60],
    ];

    this.chars.push(dude, ken);

    if (this.attacks.length == 0) {
      for (let i = 0; i < this.chars.length; i++) {
        if (this.chars[i][0] == character) {
          this.attacks.push(this.chars[i]);
        }
      }
    }
    if (key == this.attackControls[0]) {
      if (this.player.body.blocked.down) {
        if (this.attacks[0][1][0] == 'atk')
          this.attack(...this.attacks[0][1].slice(1));
        else if (this.attacks[0][1][0] == 'projectile')
          this.projectile(...this.attacks[0][1].slice(1));
      }
    }

    if (key == this.attackControls[1]) {
      if (this.player.body.blocked.down) {
        if (this.attacks[0][2][0] == 'atk')
          this.attack(...this.attacks[0][2].slice(1));
        else if (this.attacks[0][2][0] == 'projectile')
          this.projectile(...this.attacks[0][2].slice(1));
      }
    }

    if (key == this.attackControls[2]) {
      if (this.player.body.blocked.down) {
        if (this.attacks[0][3][0] == 'atk')
          this.attack(...this.attacks[0][3].slice(1));
        else if (this.attacks[0][3][0] == 'projectile')
          this.projectile(...this.attacks[0][3].slice(1));
      }
    }

    if (key == this.attackControls[3]) {
      if (this.player.body.blocked.down) {
        if (this.attacks[0][4][0] == 'atk')
          this.attack(...this.attacks[0][4].slice(1));
        else if (this.attacks[0][4][0] == 'projectile')
          this.projectile(...this.attacks[0][4].slice(1));
      }
    }

  }

  block = () => {
    if (this.enemyClass.isAttacking && !this.airdashing) {
      if ((this.direction == 'right' && this.controls[1].isDown) || (this.direction == 'left' && this.controls[3].isDown)) {
        if (this.enemyClass.atkHeight == 'mid') {
          return true;
        }

        if (this.enemyClass.atkHeight == 'high') {
          if (this.crouchBlock == false) {
            return true;
          }

          else if (this.crouchBlock == true) {
            return false;
          }
        }

        if (this.enemyClass.atkHeight == 'low') {
          if (this.crouchBlock == false) {
            return false;
          }

          else if (this.crouchBlock == true) {
            return true;
          }
        }
      }
      return false;
    }
  }

  win = () => {
    self = this;
    if (this.enemyClass.currentHp <= 0 || Math.ceil(this.timer.getRemainingSeconds()) <= 0){
      roundStart = false;
      if (Math.ceil(this.timer.getRemainingSeconds()) <= 0) {
        if (this.currentHp > this.enemyClass.currentHp){
          if (this.player == p1){
            p1wins += 1;
            if (p1wins < 2 || p2wins < 2 && wintxt){
              this.add.text(width / 2, height / 3, 'Player 1 Won This Round!', { fontFamily: 'AdrenalineHit', fontSize: 48 }).setOrigin(0.5);
            }
          }
            
          else if (this.player == p2){
            p2wins += 1;
            if (p1wins < 2 || p2wins < 2 && wintxt){
              this.add.text(width / 2, height / 3, 'Player 2 Won This Round!', { fontFamily: 'AdrenalineHit', fontSize: 48 }).setOrigin(0.5);
            }

            if (p1wins < 2 || p2wins < 2 && wintxt)
              this.add.text(width / 2, height / 2, 'Next Round in 5 Seconds', { fontFamily: 'AdrenalineHit', fontSize: 48 }).setOrigin(0.5);
          }

          wintxt = false;
          return true;
        }
          
        else if (this.currentHp == this.enemyClass.currentHp) {
          if (p1wins < 2 || p2wins < 2 && wintxt){
            this.add.text(width / 2, height / 3, 'DRAW!', { fontFamily: 'AdrenalineHit', fontSize: 48 }).setOrigin(0.5);
            this.add.text(width / 2, height / 2, 'Next Round in 5 Seconds', { fontFamily: 'AdrenalineHit', fontSize: 48 }).setOrigin(0.5);
          }

          wintxt = false;
          return false;
        }
      }
        
      else {
        if (this.player == p1){
          p1wins += 1;
          if (p1wins < 2 && p2wins < 2 && wintxt){
            this.add.text(width / 2, height / 3, 'Player 1 Won This Round!', { fontFamily: 'AdrenalineHit', fontSize: 48 }).setOrigin(0.5);
          }
        }
            
        else if (this.player == p2){
          p2wins += 1;
          if (p1wins < 2 && p2wins < 2 && wintxt){
            this.add.text(width / 2, height / 3, 'Player 2 Won This Round!', { fontFamily: 'AdrenalineHit', fontSize: 48 }).setOrigin(0.5);
          }
        }

        if (p1wins < 2 && p2wins < 2 && wintxt)
          this.add.text(width / 2, height / 2, 'Next Round in 5 Seconds', { fontFamily: 'AdrenalineHit', fontSize: 48 }).setOrigin(0.5);

        wintxt = false;
        return true;
      }

    }
  }

}

let timer;
let timerText;

function create() {
  let self = this;
  this.physics.world.setBounds(0, 0, width, height);

  this.anims.create({
    key: 'dude_left',
    frames: this.anims.generateFrameNumbers('dudesprite', { start: 0, end: 3 }),
    frameRate: 10,
    repeat: -1
  });
  this.anims.create({
    key: 'dude_turnLeft',
    frames: [{ key: 'dudesprite', frame: 0 }],
    frameRate: 20
  });
  this.anims.create({
    key: 'dude_turnRight',
    frames: [{ key: 'dudesprite', frame: 5 }],
    frameRate: 20
  });
  this.anims.create({
    key: 'dude_right',
    frames: this.anims.generateFrameNumbers('dudesprite', { start: 5, end: 8 }),
    frameRate: 10,
    repeat: -1
  });
  this.anims.create({
    key: 'dude_crouchLeft',
    frames: [{ key: 'dudesprite', frame: 9 }],
    frameRate: 20
  });
  this.anims.create({
    key: 'dude_crouchRight',
    frames: [{ key: 'dudesprite', frame: 10 }],
    frameRate: 20
  });

  this.anims.create({
    key: 'dude_headleft',
    frames: [{ key: 'dudesprite', frame: 11 }],
    frameRate: 1
  });
  this.anims.create({
    key: 'dude_punchleft',
    frames: [{ key: 'dudesprite', frame: 12 }],
    frameRate: 1
  });
  this.anims.create({
    key: 'dude_kickleft',
    frames: [{ key: 'dudesprite', frame: 13 }],
    frameRate: 1
  });
  this.anims.create({
    key: 'dude_headright',
    frames: [{ key: 'dudesprite', frame: 14 }],
    frameRate: 1
  });
  this.anims.create({
    key: 'dude_punchright',
    frames: [{ key: 'dudesprite', frame: 15 }],
    frameRate: 1
  });
  this.anims.create({
    key: 'dude_kickright',
    frames: [{ key: 'dudesprite', frame: 16 }],
    frameRate: 1
  });
  this.anims.create({
    key: 'dude_projectileleft',
    frames: [{ key: 'dudesprite', frame: 12 }],
    frameRate: 1
  });
  this.anims.create({
    key: 'dude_projectileright',
    frames: [{ key: 'dudesprite', frame: 15 }],
    frameRate: 1
  });
  this.anims.create({
    key: 'dude_projectileleft_atk',
    frames: [{ key: 'projectile', frame: 0 }],
    frameRate: 1
  });
  this.anims.create({
    key: 'dude_projectileright_atk',
    frames: [{ key: 'projectile', frame: 0 }],
    frameRate: 1
  });

  this.anims.create({
    key: 'ken_left',
    frames: this.anims.generateFrameNumbers('kensprite', { start: 91, end: 95 }),
    frameRate: 8,
    repeat: -1
  });
  this.anims.create({
    key: 'ken_turnLeft',
    frames: this.anims.generateFrameNumbers('kensprite', { start: 77, end: 80 }),
    frameRate: 8,
    repeat: -1
  });
  this.anims.create({
    key: 'ken_turnRight',
    frames: this.anims.generateFrameNumbers('kensprite', { start: 7, end: 10 }),
    frameRate: 8,
    repeat: -1
  });
  this.anims.create({
    key: 'ken_right',
    frames: this.anims.generateFrameNumbers('kensprite', { start: 21, end: 25 }),
    frameRate: 8,
    repeat: -1
  });
  this.anims.create({
    key: 'ken_crouchLeft',
    frames: [{ key: 'kensprite', frame: 133 }],
    frameRate: 20
  });
  this.anims.create({
    key: 'ken_crouchRight',
    frames: [{ key: 'kensprite', frame: 63 }],
    frameRate: 20
  });

  this.anims.create({
    key: 'ken_punchleft',
    frames: this.anims.generateFrameNumbers('kensprite', { start: 84, end: 86 }),
    frameRate: 6
  });
  this.anims.create({
    key: 'ken_kickleft',
    frames: this.anims.generateFrameNumbers('kensprite', { start: 112, end: 115 }),
    frameRate: 6
  });
  this.anims.create({
    key: 'ken_revkickleft',
    frames: this.anims.generateFrameNumbers('kensprite', { start: 119, end: 123 }),
    frameRate: 6
  });
  this.anims.create({
    key: 'ken_punchright',
    frames: this.anims.generateFrameNumbers('kensprite', { start: 14, end: 16 }),
    frameRate: 6
  });
  this.anims.create({
    key: 'ken_kickright',
    frames: this.anims.generateFrameNumbers('kensprite', { start: 42, end: 45 }),
    frameRate: 6
  });
  this.anims.create({
    key: 'ken_revkickright',
    frames: this.anims.generateFrameNumbers('kensprite', { start: 49, end: 53 }),
    frameRate: 6
  });
  this.anims.create({
    key: 'ken_hadokenleft',
    frames: this.anims.generateFrameNumbers('kensprite', { start: 70, end: 73 }),
    frameRate: 6
  });
  this.anims.create({
    key: 'ken_hadokenright',
    frames: this.anims.generateFrameNumbers('kensprite', { start: 0, end: 3 }),
    frameRate: 6
  });
  this.anims.create({
    key: 'ken_hadokenright_atk',
    frames: this.anims.generateFrameNumbers('kensprite', { start: 28, end: 29 }),
    frameRate: 6
  });
  this.anims.create({
    key: 'ken_hadokenleft_atk',
    frames: this.anims.generateFrameNumbers('kensprite', { start: 98, end: 99 }),
    frameRate: 6
  });

  if (!gameStart) {
    kenicon = this.add.image(width / 2.5, height / 2, 'ken').setScale(0.75);
    kenicon.setInteractive();
    dudeicon = this.add.image(width - (width / 2.5), height / 2, 'dude').setScale(0.1875);
    dudeicon.setInteractive();
  }

  if (gameStart) {
    this.add.image(500, 400, 'sky').setScale(1.35);

    platforms = this.physics.add.staticGroup();

    platforms.create(500, 718, 'ground').setScale(2.5, 5).refreshBody();
    p1 = this.physics.add.sprite(width / 4, 540, p1Char + 'sprite');
    p2 = this.physics.add.sprite((width * 3) / 4, 540, p2Char + 'sprite');

    if (p1Char == p2Char) {
      p2.tint = 0x575757;
    }

    timer = undefined;
    timer = this.time.addEvent({
      delay: 99000,
      loop: false
    });

    timerText = undefined;
    timerText = self.add.text(width / 2, height / 15, Math.ceil(timer.getRemainingSeconds()), { fontFamily: 'AdrenalineHit', fontSize: 48 }).setOrigin(0.5);
    setInterval(function() {
      timerText.destroy();
      timerText = self.add.text(width / 2, height / 15, Math.ceil(timer.getRemainingSeconds()), { fontFamily: 'AdrenalineHit', fontSize: 48 }).setOrigin(0.5);
    }, 1000);

    p1.setBounce(0);
    p1.setCollideWorldBounds(true);

    p2.setBounce(0);
    p2.setCollideWorldBounds(true);

    this.physics.add.collider(p1, platforms);
    this.physics.add.collider(p2, platforms);

    let p1Controls = [cursors.up, cursors.left, cursors.down, cursors.right];
    let p2Controls = [keys.W, keys.A, keys.S, keys.D];

    let p1AtkCntrls = [keys.Z, keys.X, keys.C, keys.V];
    let p2AtkCntrls = [keys.U, keys.I, keys.O, keys.P];

    player1 = new Player(p1, p2, p1Char, 100, 10, this.time, this.add, this.physics, 'right', p1Controls, p1AtkCntrls, timer);
    player2 = new Player(p2, p1, p2Char, 100, 10, this.time, this.add, this.physics, 'left', p2Controls, p2AtkCntrls, timer);

    player1.enemyClass = player2;
    player2.enemyClass = player1;

    wintxt = true;
    loaded = true;
    roundStart = true;
  }
}

let charSelectTxt;
let gotxtAdded = false

function update() {
  const self = this;
  if (!gameStart) {
    if (p1Char != undefined && p2Char != undefined) {
      gameStart = true;
    }

    else if (p1Char != undefined && p2Char == undefined) {
      if (charSelectTxt == undefined)
        charSelectTxt = this.add.text(width / 2, height / 6, 'Player 2, Select Your Character', { fontFamily: 'AdrenalineHit', fontSize: 64 }).setOrigin(0.5);

      this.input.on('gameobjectup', function(pointer, char) {
        if (p2Char == undefined) {
          p2Char = char.texture.key;
          charSelectTxt.destroy();
          charSelectTxt = undefined
        }
      });
    }

    else if (p1Char == undefined) {
      if (charSelectTxt == undefined)
        charSelectTxt = this.add.text(width / 2, height / 6, 'Player 1, Select Your Character', { fontFamily: 'AdrenalineHit', fontSize: 64 }).setOrigin(0.5);

      this.input.on('gameobjectup', function(pointer, char) {
        if (p1Char == undefined) {
          p1Char = char.texture.key;
          charSelectTxt.destroy();
          charSelectTxt = undefined
        }

      });
    }

  }
  if (gameStart) {
    if (!loaded) {
      roundStart = true;
      this.registry.destroy();
      this.events.off();
      this.scene.restart();
    }

    if (loaded) {
      for (let i = 0; i < player1.attackControls.length; i++) {
        if (!player1.isAttacking && !player1.isDashing && player1 && !player1.stun && roundStart) {
          if (justDown(player1.attackControls[i])) {
            player1.attackController(player1.attackControls[i], player1.char);
          }
        }
      }

      for (let i = 0; i < player2.attackControls.length; i++) {
        if (!player2.isAttacking && !player2.isDashing && player2 && !player1.stun && roundStart) {
          if (justDown(player2.attackControls[i])) {
            player2.attackController(player2.attackControls[i], player2.char);
          }
        }
      }

      if (!player1.isAtkGrounded && !player1.stun && roundStart)
        player1.movement();
      if (!player2.isAtkGrounded && !player2.stun && roundStart)
        player2.movement();

      player1.healthbar();
      player2.healthbar();

      player1.winCounter();
      player2.winCounter();

      if (player1.win() || player2.win() || (!roundStart && (!player1.win() || !player2.win()))) {

        player1.currentHp = player1.maxHp;
        player2.currentHp = player2.maxHp;
        timer.paused = true;

        if (p1wins >= 2 || p2wins >= 2 && !gotxtAdded){
          this.add.text(width / 2, height / 3, 'Game Over!', { fontFamily: 'AdrenalineHit', fontSize: 48 }).setOrigin(0.5);

          if (p1wins >= 2)
            this.add.text(width / 2, height / 2, 'Player 1 Won!', { fontFamily: 'AdrenalineHit', fontSize: 48 }).setOrigin(0.5);

          if (p2wins >= 2)
            this.add.text(width / 2, height / 2, 'Player 2 Won!', { fontFamily: 'AdrenalineHit', fontSize: 48 }).setOrigin(0.5);

          gotxtAdded = true;
        }
        
        if (p1wins < 2 && p2wins < 2){
          this.time.delayedCall(5000, function(){
            self.registry.destroy();
            self.events.off();
            self.scene.restart();
          });
        }
      }
    }
  }
}