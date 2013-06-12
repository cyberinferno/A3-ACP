USE [ASD]
GO

/****** Object:  Table [dbo].[Deals]    Script Date: 06/07/2013 19:55:57 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[Deals](
	[deal_id] [int] NOT NULL,
	[character] [varchar](50) NOT NULL,
	[item_name] [varchar](100) NOT NULL,
	[item_code] [varchar](4000) NOT NULL,
	[flamez_coins] [float] NOT NULL,
	[deal_status] [int] NOT NULL,
	[bcharacter] [varchar](100) NULL,
	[deal_time] [varchar](50) NULL,
	[seller_ip] [varchar](50) NOT NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

ALTER TABLE [dbo].[Deals] ADD  CONSTRAINT [DF_Deals_flamez_coins]  DEFAULT ((0)) FOR [flamez_coins]
GO

ALTER TABLE [dbo].[Deals] ADD  CONSTRAINT [DF_Deals_sold]  DEFAULT ((1)) FOR [deal_status]
GO

ALTER TABLE [dbo].[Deals] ADD  CONSTRAINT [DF_Deals_bcharacter]  DEFAULT ('none') FOR [bcharacter]
GO

ALTER TABLE [dbo].[Deals] ADD  CONSTRAINT [DF_Deals_seller_ip]  DEFAULT ('127.0.0.1') FOR [seller_ip]
GO

