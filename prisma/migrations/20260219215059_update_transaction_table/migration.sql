/*
  Warnings:

  - A unique constraint covering the columns `[orderId]` on the table `Transaction` will be added. If there are existing duplicate values, this will fail.
  - Added the required column `orderId` to the `Transaction` table without a default value. This is not possible if the table is not empty.
  - Added the required column `redirectUrl` to the `Transaction` table without a default value. This is not possible if the table is not empty.
  - Added the required column `token` to the `Transaction` table without a default value. This is not possible if the table is not empty.

*/
-- AlterTable
ALTER TABLE `transaction` ADD COLUMN `orderId` VARCHAR(191) NOT NULL,
    ADD COLUMN `redirectUrl` VARCHAR(191) NOT NULL,
    ADD COLUMN `token` TEXT NOT NULL,
    MODIFY `paymentMethod` VARCHAR(191) NULL;

-- CreateIndex
CREATE UNIQUE INDEX `Transaction_orderId_key` ON `Transaction`(`orderId`);
