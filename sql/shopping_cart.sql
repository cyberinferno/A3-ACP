USE [ASD]
GO

/****** Object:  Table [dbo].[shopping_cart]    Script Date: 06/07/2013 19:56:59 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[shopping_cart](
	[char_name] [varchar](255) NOT NULL,
	[item_ids] [varchar](255) NOT NULL,
	[credits_required] [bigint] NOT NULL,
	[coupon_code] [varchar](255) NOT NULL,
	[discount] [bigint] NOT NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

ALTER TABLE [dbo].[shopping_cart] ADD  CONSTRAINT [DF_shopping_cart_coupon_code]  DEFAULT ('NIL') FOR [coupon_code]
GO

ALTER TABLE [dbo].[shopping_cart] ADD  CONSTRAINT [DF_shopping_cart_discount]  DEFAULT ((0)) FOR [discount]
GO

